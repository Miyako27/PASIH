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

        $submissionQuery = Submission::query();
        $assignmentQuery = Assignment::query();

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

        $acceptedSubmissions = (clone $submissionQuery)->where('status', 'accepted')->count();
        $inAnalysisAssignments = (clone $assignmentQuery)
            ->whereIn('status', ['in_progress', 'pending_kadiv_approval', 'pending_kakanwil_approval', 'revision_by_pic'])
            ->count();
        $completedAssignmentsCount = (clone $assignmentQuery)->where('status', 'completed')->count();
        $validatedSubmissions = (clone $submissionQuery)
            ->whereIn('status', ['accepted', 'disposed', 'assigned', 'completed'])
            ->count();
        $disposedSubmissions = (clone $submissionQuery)
            ->where(function ($query) {
                $query
                    ->where('status', 'disposed')
                    ->orWhereHas('assignments');
            })
            ->count();

        $stats = [
            'total_submissions' => (clone $submissionQuery)->count(),
            'submitted' => (clone $submissionQuery)->where('status', 'submitted')->count(),
            'in_progress' => $acceptedSubmissions,
            'in_analysis' => $inAnalysisAssignments,
            'completed' => $completedAssignmentsCount,
            'total_assignments' => (clone $assignmentQuery)->count(),
        ];

        $recentSubmissions = $submissionQuery->with('submitter')->latest()->limit(6)->get();

        $bottleneck = [
            'Permohonan Masuk' => (clone $submissionQuery)->count(),
            'Sudah Divalidasi' => $validatedSubmissions,
            'Sudah Disposisi' => $disposedSubmissions,
            'Sedang Dianalisis' => $inAnalysisAssignments,
            'Selesai' => $completedAssignmentsCount,
        ];

        $completedAssignments = (clone $assignmentQuery)
            ->where('status', 'completed')
            ->whereNotNull('assigned_at')
            ->whereNotNull('completed_at')
            ->get();

        $onTime = $completedAssignments->filter(function (Assignment $assignment): bool {
            return $assignment->assigned_at !== null
                && $assignment->completed_at !== null
                && $assignment->completed_at->diffInDays($assignment->assigned_at) <= 3;
        })->count();
        $late = max($completedAssignments->count() - $onTime, 0);

        return view('pages.dashboard', [
            'stats' => $stats,
            'recentSubmissions' => $recentSubmissions,
            'bottleneck' => $bottleneck,
            'punctuality' => [
                'on_time' => $onTime,
                'late' => $late,
                'total' => $completedAssignments->count(),
            ],
            'recentActivities' => NotificationController::buildActivities($user, 10),
        ]);
    }
}
