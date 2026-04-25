<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\AssignmentAnalysisApproval;
use App\Models\AssignmentDocument;
use App\Models\AssignmentPicUpdate;
use App\Models\Submission;
use App\Models\SubmissionStatusLog;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $this->buildNotifications($user, 30);

        // Menandai notifikasi sebagai sudah dilihat saat halaman notifikasi dibuka.
        $user->forceFill([
            'notifications_seen_at' => now(),
        ])->saveQuietly();

        return view('pages.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * @return Collection<int, array{
     *   type:string,
     *   title:string,
     *   detail:string,
     *   user_id:int|null,
     *   user:string,
     *   time:\Illuminate\Support\Carbon|null,
     *   url:string|null
     * }>
     */
    public static function buildNotifications($user, int $limit = 10): Collection
    {
        if ($user->role->value === 'admin') {
            return collect();
        }

        $role = $user->role->value;
        $includeSubmissionNotifications = ! in_array($role, ['ketua_tim_analisis', 'analis_hukum'], true);
        $includeAssignmentNotifications = true;
        $assignmentStatuses = null;

        if ($role === 'operator_kanwil') {
            // Operator Kanwil: notifikasi permohonan + status analisis selesai saja.
            $assignmentStatuses = ['completed'];
        }

        if ($role === 'operator_pemda' || $role === 'analis_hukum') {
            // Operator Pemda dan Analis Hukum: terima seluruh status analisis (bukan hanya selesai).
            $assignmentStatuses = array_map(
                static fn (AssignmentStatus $status) => $status->value,
                AssignmentStatus::cases()
            );
        }

        $submissionQuery = Submission::query()
            ->select([
                'id',
                'nomor_surat',
                'submitter_id',
                'updated_at',
            ]);

        $assignmentQuery = Assignment::query()
            ->select(['id', 'submission_id', 'status', 'assigned_by_id', 'created_at', 'updated_at'])
            ->with(['submission:id,nomor_surat,submitter_id']);

        if ($role === 'operator_pemda') {
            $submissionQuery->where('submitter_id', $user->id);
            $assignmentQuery->whereHas('submission', function ($query) use ($user) {
                $query->where('submitter_id', $user->id);
            });
        }

        if ($role === 'analis_hukum') {
            $assignmentQuery->whereAnalyst($user->id);
            $submissionQuery->whereHas('assignments', function ($query) use ($user) {
                $query->whereAnalyst($user->id);
            });
        }

        $submissionEventRows = collect();
        $submissionMeta = collect();

        if ($includeSubmissionNotifications) {
            $submissionScopeLimit = max($limit * 12, 120);
            $submissionIds = (clone $submissionQuery)
                ->latest('updated_at')
                ->limit($submissionScopeLimit)
                ->pluck('id');

            if ($submissionIds->isNotEmpty()) {
                $scopedSubmissions = (clone $submissionQuery)
                    ->whereIn('id', $submissionIds)
                    ->get()
                    ->keyBy('id');

                $submissionMeta = $scopedSubmissions->map(function (Submission $submission) {
                    return [
                        'submission_id' => $submission->id,
                        'nomor_surat' => $submission->nomor_surat,
                        'submitter_id' => $submission->submitter_id,
                    ];
                });

                $submissionEventRows = SubmissionStatusLog::query()
                    ->select(['submission_id', 'kanwil_operator_id', 'status', 'created_at'])
                    ->whereIn('submission_id', $submissionIds)
                    ->get()
                    ->map(function (SubmissionStatusLog $statusLog) use ($submissionMeta) {
                        $meta = $submissionMeta->get($statusLog->submission_id);
                        $actorId = $statusLog->kanwil_operator_id;

                        if (! $actorId && is_array($meta)) {
                            $actorId = $meta['submitter_id'] ?? null;
                        }

                        return [
                            'submission_id' => $statusLog->submission_id,
                            'status' => (string) $statusLog->status,
                            'actor_id' => $actorId,
                            'time' => $statusLog->created_at,
                        ];
                    })
                    ->filter(function ($row) {
                        return isset($row['submission_id'], $row['status'], $row['time']);
                    })
                    ->values();
            }
        }

        $assignmentEventRows = collect();
        $assignmentMeta = collect();

        if ($includeAssignmentNotifications) {
            $assignmentScopeLimit = max($limit * 12, 120);
            $assignmentIds = (clone $assignmentQuery)
                ->latest('updated_at')
                ->limit($assignmentScopeLimit)
                ->pluck('id');

            if ($assignmentIds->isNotEmpty()) {
                $scopedAssignments = (clone $assignmentQuery)
                    ->whereIn('id', $assignmentIds)
                    ->get()
                    ->keyBy('id');

                $assignmentMeta = $scopedAssignments->map(function (Assignment $assignment) {
                    return [
                        'assignment_id' => $assignment->id,
                        'submission_id' => $assignment->submission_id,
                        'nomor_surat' => $assignment->submission?->nomor_surat ?? ('#'.$assignment->id),
                    ];
                });

                $assignedEvents = $scopedAssignments->map(function (Assignment $assignment) {
                    return [
                        'assignment_id' => $assignment->id,
                        'status' => 'assigned',
                        'actor_id' => $assignment->assigned_by_id,
                        'time' => $assignment->created_at,
                    ];
                });

                $inProgressEvents = AssignmentPicUpdate::query()
                    ->select(['assignment_id', 'pic_assigned_by_id', 'created_at'])
                    ->whereIn('assignment_id', $assignmentIds)
                    ->get()
                    ->map(function (AssignmentPicUpdate $update) {
                        return [
                            'assignment_id' => $update->assignment_id,
                            'status' => 'in_progress',
                            'actor_id' => $update->pic_assigned_by_id,
                            'time' => $update->created_at,
                        ];
                    });

                $pendingKadivEvents = AssignmentDocument::query()
                    ->select(['assignment_id', 'uploaded_by', 'created_at'])
                    ->whereIn('assignment_id', $assignmentIds)
                    ->where('document_type', 'hasil_analisis')
                    ->get()
                    ->map(function (AssignmentDocument $document) {
                        return [
                            'assignment_id' => $document->assignment_id,
                            'status' => 'pending_kadiv_approval',
                            'actor_id' => $document->uploaded_by,
                            'time' => $document->created_at,
                        ];
                    });

                $approvalEvents = AssignmentAnalysisApproval::query()
                    ->select([
                        'assignment_id',
                        'assigned_by_id',
                        'revision_note',
                        'approved_by_kadiv_at',
                        'approved_by_kakanwil_at',
                        'created_at',
                    ])
                    ->whereIn('assignment_id', $assignmentIds)
                    ->get()
                    ->map(function (AssignmentAnalysisApproval $approval) {
                        if ($approval->approved_by_kakanwil_at !== null) {
                            return [
                                'assignment_id' => $approval->assignment_id,
                                'status' => 'completed',
                                'actor_id' => $approval->assigned_by_id,
                                'time' => $approval->approved_by_kakanwil_at,
                            ];
                        }

                        if ($approval->approved_by_kadiv_at !== null) {
                            return [
                                'assignment_id' => $approval->assignment_id,
                                'status' => 'pending_kakanwil_approval',
                                'actor_id' => $approval->assigned_by_id,
                                'time' => $approval->approved_by_kadiv_at,
                            ];
                        }

                        if (filled($approval->revision_note)) {
                            return [
                                'assignment_id' => $approval->assignment_id,
                                'status' => 'revision_by_pic',
                                'actor_id' => $approval->assigned_by_id,
                                'time' => $approval->created_at,
                            ];
                        }

                        return null;
                    })
                    ->filter();

                $assignmentEventRows = $assignedEvents
                    ->concat($inProgressEvents)
                    ->concat($pendingKadivEvents)
                    ->concat($approvalEvents)
                    ->filter(function ($row) {
                        return isset($row['assignment_id'], $row['status'], $row['time']);
                    });

                if (is_array($assignmentStatuses) && $assignmentStatuses !== []) {
                    $assignmentEventRows = $assignmentEventRows
                        ->filter(fn ($row) => in_array((string) $row['status'], $assignmentStatuses, true))
                        ->values();
                }
            }
        }

        $userIds = $submissionEventRows
            ->pluck('actor_id')
            ->concat($assignmentEventRows->pluck('actor_id'))
            ->filter()
            ->unique()
            ->values();

        $userNames = User::query()
            ->whereIn('id', $userIds)
            ->pluck('name', 'id');

        $submissionNotifications = $submissionEventRows->map(function ($event) use ($submissionMeta, $userNames, $user) {
            $submissionId = (int) $event['submission_id'];
            $meta = $submissionMeta->get($submissionId);
            if (! is_array($meta)) {
                return null;
            }

            $nomorSurat = (string) ($meta['nomor_surat'] ?? ('#'.$submissionId));
            $status = (string) $event['status'];
            $actorId = isset($event['actor_id']) ? (int) $event['actor_id'] : null;
            $actorName = $actorId ? ($userNames->get($actorId) ?? 'Sistem') : 'Sistem';

            if (in_array($status, ['accepted', 'revised', 'rejected'], true)) {
                return [
                    'type' => 'Status Permohonan',
                    'title' => "Perubahan status permohonan {$nomorSurat}",
                    'detail' => 'Status sekarang: '.self::resolveSubmissionStatusLabel($status),
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $event['time'],
                    'url' => self::resolveSubmissionUrl($user, $submissionId),
                ];
            }

            if ($status === 'disposed') {
                return [
                    'type' => 'Disposisi',
                    'title' => "Permohonan {$nomorSurat} telah didisposisikan",
                    'detail' => 'Permohonan diteruskan untuk proses berikutnya.',
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $event['time'],
                    'url' => self::resolveSubmissionUrl($user, $submissionId),
                ];
            }

            return [
                'type' => 'Notifikasi Lainnya',
                'title' => "Pembaruan permohonan {$nomorSurat}",
                'detail' => 'Status saat ini: '.self::resolveSubmissionStatusLabel($status),
                'user_id' => $actorId,
                'user' => $actorName,
                'time' => $event['time'],
                'url' => self::resolveSubmissionUrl($user, $submissionId),
            ];
        })->filter()->values();

        $assignmentNotifications = $assignmentEventRows->map(function ($event) use ($assignmentMeta, $userNames, $user) {
            $assignmentId = (int) $event['assignment_id'];
            $meta = $assignmentMeta->get($assignmentId);
            if (! is_array($meta)) {
                return null;
            }

            $nomorSurat = (string) ($meta['nomor_surat'] ?? ('#'.$assignmentId));
            $submissionId = isset($meta['submission_id']) ? (int) $meta['submission_id'] : null;
            $status = (string) $event['status'];
            $actorId = isset($event['actor_id']) ? (int) $event['actor_id'] : null;
            $actorName = $actorId ? ($userNames->get($actorId) ?? 'Sistem') : 'Sistem';

            if ($status === 'assigned') {
                return [
                    'type' => 'Penugasan',
                    'title' => "Ada penugasan baru untuk {$nomorSurat}",
                    'detail' => 'Status analisis: Belum ada Penanggung Jawab',
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $event['time'],
                    'url' => self::resolveAssignmentUrl($user, $assignmentId, $submissionId),
                ];
            }

            if (in_array($status, ['in_progress', 'pending_kadiv_approval', 'pending_kakanwil_approval', 'revision_by_pic', 'completed'], true)) {
                return [
                    'type' => 'Status Analisis',
                    'title' => "Perubahan status analisis {$nomorSurat}",
                    'detail' => 'Status sekarang: '.self::resolveAssignmentStatusLabel($status),
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $event['time'],
                    'url' => self::resolveAssignmentUrl($user, $assignmentId, $submissionId),
                ];
            }

            return [
                'type' => 'Notifikasi Lainnya',
                'title' => "Pembaruan penugasan {$nomorSurat}",
                'detail' => 'Status saat ini: '.self::resolveAssignmentStatusLabel($status),
                'user_id' => $actorId,
                'user' => $actorName,
                'time' => $event['time'],
                'url' => self::resolveAssignmentUrl($user, $assignmentId, $submissionId),
            ];
        })->filter()->values();

        return $submissionNotifications
            ->concat($assignmentNotifications)
            ->sortByDesc('time')
            ->take($limit)
            ->values();
    }

    public static function countUnreadNotifications($user, int $limit = 30): int
    {
        if ($user->role->value === 'admin') {
            return 0;
        }

        $notifications = self::buildNotifications($user, $limit);
        $seenAt = $user->notifications_seen_at;

        if ($seenAt === null) {
            return $notifications->count();
        }

        return $notifications
            ->filter(function ($item) use ($seenAt): bool {
                return isset($item['time']) && $item['time'] !== null && $item['time']->gt($seenAt);
            })
            ->count();
    }

    private static function resolveSubmissionUrl($user, int $submissionId): ?string
    {
        return route('submissions.show', $submissionId);
    }

    private static function resolveSubmissionStatusLabel(string $status): string
    {
        return SubmissionStatus::tryFrom($status)?->label() ?? ucfirst(str_replace('_', ' ', $status));
    }

    private static function resolveAssignmentStatusLabel(string $status): string
    {
        return AssignmentStatus::tryFrom($status)?->label() ?? ucfirst(str_replace('_', ' ', $status));
    }

    private static function resolveAssignmentUrl($user, int $assignmentId, ?int $submissionId): ?string
    {
        $role = $user->role->value;

        if ($role === 'operator_pemda' && $submissionId !== null) {
            return route('submissions.show', $submissionId);
        }

        if (in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true)) {
            return route('assignments.show', $assignmentId);
        }

        return null;
    }

    public static function buildActivities($user, int $limit = 10): Collection
    {
        return UserActivity::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (UserActivity $activity) use ($user) {
                return [
                    'type' => $activity->type,
                    'title' => $activity->title,
                    'detail' => $activity->detail ?? '-',
                    'user_id' => $user->id,
                    'user' => $user->name,
                    'time' => $activity->created_at,
                ];
            })
            ->values();
    }
}
