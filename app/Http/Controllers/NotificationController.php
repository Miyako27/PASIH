<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $this->buildNotifications($request->user(), 30);

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
     *   time:\Illuminate\Support\Carbon|null
     * }>
     */
    public static function buildNotifications($user, int $limit = 10): Collection
    {
        $submissionQuery = Submission::query()
            ->select([
                'id',
                'nomor_surat',
                'status',
                'submitter_id',
                'kanwil_operator_id',
                'division_operator_id',
                'updated_at',
            ]);

        $assignmentQuery = Assignment::query()
            ->select(['id', 'submission_id', 'status', 'assigned_by_id', 'analyst_id', 'updated_at'])
            ->with('submission:id,nomor_surat');

        if ($user->role->value === 'operator_pemda') {
            $submissionQuery->where('submitter_id', $user->id);
            $assignmentQuery->whereHas('submission', function ($query) use ($user) {
                $query->where('submitter_id', $user->id);
            });
        }

        if ($user->role->value === 'analis_hukum') {
            $assignmentQuery->where('analyst_id', $user->id);
            $submissionQuery->whereHas('assignments', function ($query) use ($user) {
                $query->where('analyst_id', $user->id);
            });
        }

        $submissions = $submissionQuery
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        $assignments = $assignmentQuery
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        $userIds = $submissions
            ->flatMap(fn (Submission $item) => [$item->submitter_id, $item->kanwil_operator_id, $item->division_operator_id])
            ->concat($assignments->flatMap(fn (Assignment $item) => [$item->assigned_by_id, $item->analyst_id]))
            ->filter()
            ->unique()
            ->values();

        $userNames = User::query()
            ->whereIn('id', $userIds)
            ->pluck('name', 'id');

        $submissionNotifications = $submissions->map(function (Submission $submission) use ($userNames) {
            $status = $submission->status->value;
            $actorId = $submission->kanwil_operator_id ?? $submission->division_operator_id ?? $submission->submitter_id;
            $actorName = $userNames->get($actorId) ?? 'Sistem';

            if (in_array($status, ['accepted', 'revised', 'rejected'], true)) {
                return [
                    'type' => 'Status Permohonan',
                    'title' => "Perubahan status permohonan {$submission->nomor_surat}",
                    'detail' => "Status sekarang: {$submission->status->label()}",
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $submission->updated_at,
                ];
            }

            if ($status === 'disposed') {
                return [
                    'type' => 'Disposisi',
                    'title' => "Permohonan {$submission->nomor_surat} telah didisposisikan",
                    'detail' => 'Permohonan diteruskan untuk proses berikutnya.',
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $submission->updated_at,
                ];
            }

            return [
                'type' => 'Notifikasi Lainnya',
                'title' => "Pembaruan permohonan {$submission->nomor_surat}",
                'detail' => "Status saat ini: {$submission->status->label()}",
                'user_id' => $actorId,
                'user' => $actorName,
                'time' => $submission->updated_at,
            ];
        });

        $assignmentNotifications = $assignments->map(function (Assignment $assignment) use ($userNames) {
            $nomorSurat = $assignment->submission?->nomor_surat ?? ('#'.$assignment->id);
            $status = $assignment->status->value;
            $actorId = in_array($status, ['in_progress', 'completed'], true)
                ? ($assignment->analyst_id ?? $assignment->assigned_by_id)
                : ($assignment->assigned_by_id ?? $assignment->analyst_id);
            $actorName = $userNames->get($actorId) ?? 'Sistem';

            if ($status === 'assigned') {
                return [
                    'type' => 'Penugasan',
                    'title' => "Ada penugasan baru untuk {$nomorSurat}",
                    'detail' => 'Status analisis: Tersedia',
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $assignment->updated_at,
                ];
            }

            if (in_array($status, ['in_progress', 'completed'], true)) {
                return [
                    'type' => 'Status Analisis',
                    'title' => "Perubahan status analisis {$nomorSurat}",
                    'detail' => "Status sekarang: {$assignment->status->label()}",
                    'user_id' => $actorId,
                    'user' => $actorName,
                    'time' => $assignment->updated_at,
                ];
            }

            return [
                'type' => 'Notifikasi Lainnya',
                'title' => "Pembaruan penugasan {$nomorSurat}",
                'detail' => "Status saat ini: {$assignment->status->label()}",
                'user_id' => $actorId,
                'user' => $actorName,
                'time' => $assignment->updated_at,
            ];
        });

        return $submissionNotifications
            ->concat($assignmentNotifications)
            ->sortByDesc('time')
            ->take($limit)
            ->values();
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
