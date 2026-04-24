<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Instansi;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role->value === 'admin') {
            return view('pages.admin.dashboard', [
                'stats' => [
                    'total_accounts' => User::query()->count(),
                    'total_instansi' => Instansi::query()->count(),
                    'total_submissions' => Submission::query()->count(),
                    'total_assignments' => Assignment::query()->count(),
                ],
                'recentAccounts' => User::query()->with('instansi')->latest()->limit(5)->get(),
                'recentActivities' => NotificationController::buildActivities($user, 10),
            ]);
        }

        $periodStart = now()->subYear();

        $submissionQuery = Submission::query();
        $assignmentQuery = Assignment::query();

        if ($user->role->value === 'operator_pemda') {
            $submissionQuery->where('submitter_id', $user->id);
            $assignmentQuery->whereHas('submission', function ($query) use ($user) {
                $query->where('submitter_id', $user->id);
            });
        }

        if ($user->role->value === 'analis_hukum') {
            $assignmentQuery->whereAnalyst($user->id);
            $submissionQuery->whereHas('assignments', function ($query) use ($user) {
                $query->whereAnalyst($user->id);
            });
        }

        $periodSubmissionQuery = (clone $submissionQuery)
            ->where('created_at', '>=', $periodStart);
        $periodAssignmentQuery = (clone $assignmentQuery)
            ->whereHas('submission', function ($query) use ($periodStart) {
                $query->where('created_at', '>=', $periodStart);
            });

        $acceptedSubmissions = (clone $periodSubmissionQuery)->whereStatus('accepted')->count();
        $completedSubmissionsCount = (clone $periodSubmissionQuery)->whereStatus('completed')->count();
        $inAnalysisAssignments = (clone $periodAssignmentQuery)
            ->whereIn('status', ['in_progress', 'pending_kadiv_approval', 'pending_kakanwil_approval', 'revision_by_pic'])
            ->count();
        $completedAssignmentsCount = (clone $periodAssignmentQuery)->where('status', 'completed')->count();
        $validatedSubmissions = (clone $periodSubmissionQuery)
            ->whereStatusIn(['accepted', 'disposed', 'assigned', 'completed'])
            ->count();
        $disposedSubmissions = (clone $periodSubmissionQuery)
            ->where(function ($query) {
                $query
                    ->whereStatus('disposed')
                    ->orWhereHas('assignments');
            })
            ->count();

        $stats = [
            'total_submissions' => (clone $periodSubmissionQuery)->count(),
            'submitted' => (clone $periodSubmissionQuery)->whereStatus('submitted')->count(),
            'in_progress' => $acceptedSubmissions,
            'in_analysis' => $inAnalysisAssignments,
            'completed' => $completedAssignmentsCount,
            'completed_submissions' => $completedSubmissionsCount,
            'total_assignments' => (clone $periodAssignmentQuery)->count(),
        ];

        $summarySubmissionQuery = (clone $periodSubmissionQuery);
        $summaryStats = [
            'total_submissions' => (clone $summarySubmissionQuery)->count(),
            'completed_submissions' => (clone $summarySubmissionQuery)->whereStatus('completed')->count(),
        ];

        $recentSubmissions = (clone $periodSubmissionQuery)->with('submitter')->latest()->limit(6)->get();

        $bottleneck = [
            'Permohonan Masuk' => (clone $periodSubmissionQuery)->count(),
            'Sudah Divalidasi' => $validatedSubmissions,
            'Sudah Disposisi' => $disposedSubmissions,
            'Belum Ada Penanggung Jawab' => (clone $periodAssignmentQuery)->where('status', 'assigned')->count(),
            'Sedang Dianalisis' => (clone $periodAssignmentQuery)->whereIn('status', ['in_progress', 'revision_by_pic'])->count(),
            'Menunggu Persetujuan Kepala Divisi P3H' => (clone $periodAssignmentQuery)->where('status', 'pending_kadiv_approval')->count(),
            'Menunggu Persetujuan Kepala Kantor Wilayah' => (clone $periodAssignmentQuery)->where('status', 'pending_kakanwil_approval')->count(),
            'Selesai Analisis' => $completedAssignmentsCount,
        ];

        $preferredInstitutionOrder = [
            'Pemerintah Provinsi Riau',
            'Kota Pekanbaru',
            'Kota Dumai',
            'Kabupaten Kampar',
            'Kabupaten Siak',
            'Kabupaten Pelalawan',
            'Kabupaten Kuantan Singingi',
            'Kabupaten Rokan Hulu',
            'Kabupaten Rokan Hilir',
            'Kabupaten Indragiri Hilir',
            'Kabupaten Indragiri Hulu',
            'Kabupaten Kepulauan Meranti',
            'Kabupaten Bengkalis',
        ];

        $preferredInstitutionIndex = array_flip($preferredInstitutionOrder);

        $institutionSubmissionCounts = Instansi::query()
            ->leftJoin('users', 'users.id_instansi', '=', 'instansi.id_instansi')
            ->leftJoin('submissions', function ($join) use ($periodStart) {
                $join->on('submissions.submitter_id', '=', 'users.id')
                    ->where('submissions.created_at', '>=', $periodStart);
            })
            ->groupBy('instansi.id_instansi', 'instansi.nama_instansi')
            ->select('instansi.nama_instansi')
            ->selectRaw('COUNT(submissions.id) as total_permohonan')
            ->get()
            ->sort(function ($a, $b) use ($preferredInstitutionIndex) {
                $indexA = $preferredInstitutionIndex[$a->nama_instansi] ?? PHP_INT_MAX;
                $indexB = $preferredInstitutionIndex[$b->nama_instansi] ?? PHP_INT_MAX;

                if ($indexA === $indexB) {
                    return strcmp($a->nama_instansi, $b->nama_instansi);
                }

                return $indexA <=> $indexB;
            })
            ->values();

        $taskNotifications = match ($user->role->value) {
            'operator_pemda' => [
                [
                    'title' => 'Perbaiki permohonan yang direvisi',
                    'description' => 'Permohonan perlu diperbaiki lalu dikirim ulang',
                    'count' => (clone $submissionQuery)->whereStatus('revised')->count(),
                    'url' => route('submissions.index', ['status' => 'revised']),
                ],
                [
                    'title' => 'Pantau permohonan menunggu validasi',
                    'description' => 'Permohonan masih menunggu proses validasi operator kanwil',
                    'count' => (clone $submissionQuery)->whereStatus('submitted')->count(),
                    'url' => route('submissions.index', ['status' => 'submitted']),
                ],
            ],
            'operator_kanwil' => [
                [
                    'title' => 'Validasi permohonan masuk',
                    'description' => 'Permohonan berstatus diajukan/revisi menunggu validasi dan disposisi',
                    'count' => Submission::query()->whereStatusIn(['submitted', 'revised'])->count(),
                    'url' => route('submissions.index'),
                ],
                [
                    'title' => 'Lanjutkan disposisi permohonan diterima',
                    'description' => 'Permohonan diterima namun belum didisposisikan ke Kadiv',
                    'count' => Submission::query()
                        ->whereStatus('accepted')
                        ->whereDoesntHave('dispositions')
                        ->count(),
                    'url' => route('submissions.index', ['status' => 'accepted']),
                ],
            ],
            'ketua_tim_analisis' => [
                [
                    'title' => 'Tentukan Penanggung Jawab analisis',
                    'description' => 'Penugasan sudah dibuat tetapi Penanggung Jawab belum ditentukan',
                    'count' => (clone $assignmentQuery)->where('status', 'assigned')->count(),
                    'url' => route('assignments.index', ['status' => 'assigned']),
                ],
                [
                    'title' => 'Pantau revisi dari Penanggung Jawab',
                    'description' => 'Penugasan direvisi dan perlu dipantau progres pembaruannya',
                    'count' => (clone $assignmentQuery)->where('status', 'revision_by_pic')->count(),
                    'url' => route('assignments.index', ['status' => 'revision_by_pic']),
                ],
            ],
            'analis_hukum' => [
                [
                    'title' => 'Kerjakan analisis aktif',
                    'description' => 'Penugasan dalam proses analisis dan menunggu unggahan hasil',
                    'count' => (clone $assignmentQuery)->where('status', 'in_progress')->count(),
                    'url' => route('assignments.index', ['status' => 'in_progress']),
                ],
                [
                    'title' => 'Tindak lanjuti revisi',
                    'description' => 'Hasil analisis dikembalikan dan perlu diperbarui',
                    'count' => (clone $assignmentQuery)->where('status', 'revision_by_pic')->count(),
                    'url' => route('assignments.index', ['status' => 'revision_by_pic']),
                ],
            ],
            'kepala_divisi_p3h' => [
                [
                    'title' => 'Setujui hasil analisis',
                    'description' => 'Penugasan menunggu persetujuan Kadiv',
                    'count' => (clone $assignmentQuery)->where('status', 'pending_kadiv_approval')->count(),
                    'url' => route('assignments.index', ['status' => 'pending_kadiv_approval']),
                ],
                [
                    'title' => 'Buat penugasan baru',
                    'description' => 'Permohonan sudah siap namun belum dibuat penugasan',
                    'count' => (clone $submissionQuery)
                        ->whereStatusIn(['accepted', 'disposed', 'assigned'])
                        ->whereDoesntHave('assignments')
                        ->count(),
                    'url' => route('submissions.index'),
                ],
            ],
            'kakanwil' => [
                [
                    'title' => 'Setujui final hasil analisis',
                    'description' => 'Penugasan menunggu persetujuan final Kakanwil',
                    'count' => (clone $assignmentQuery)->where('status', 'pending_kakanwil_approval')->count(),
                    'url' => route('assignments.index', ['status' => 'pending_kakanwil_approval']),
                ],
                [
                    'title' => 'Buat penugasan baru',
                    'description' => 'Permohonan sudah siap namun belum dibuat penugasan',
                    'count' => (clone $submissionQuery)
                        ->whereStatusIn(['accepted', 'disposed', 'assigned'])
                        ->whereDoesntHave('assignments')
                        ->count(),
                    'url' => route('submissions.index'),
                ],
            ],
            default => [],
        };

        $completedAssignments = (clone $periodAssignmentQuery)
            ->where('status', 'completed')
            ->get();

        $onTime = $completedAssignments->filter(function (Assignment $assignment): bool {
            return $assignment->assigned_at !== null
                && $assignment->completed_at !== null
                && $assignment->completed_at->diffInDays($assignment->assigned_at) <= 3;
        })->count();
        $late = max($completedAssignments->count() - $onTime, 0);

        return view('pages.dashboard', [
            'stats' => $stats,
            'summaryStats' => $summaryStats,
            'recentSubmissions' => $recentSubmissions,
            'bottleneck' => $bottleneck,
            'taskNotifications' => $taskNotifications,
            'institutionSubmissionCounts' => $institutionSubmissionCounts,
            'punctuality' => [
                'on_time' => $onTime,
                'late' => $late,
                'total' => $completedAssignments->count(),
            ],
            'recentActivities' => NotificationController::buildActivities($user, 10),
        ]);
    }
}
