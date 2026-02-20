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
            ]);
        }

        $submissionQuery = Submission::query();
        $assignmentQuery = Assignment::query();

        if ($user->role->value === 'operator_pemda') {
            $submissionQuery->where('submitter_id', $user->id);
        }

        if ($user->role->value === 'analis_hukum') {
            $assignmentQuery->where('analyst_id', $user->id);
            $submissionQuery->whereHas('assignments', function ($query) use ($user) {
                $query->where('analyst_id', $user->id);
            });
        }

        $stats = [
            'total_submissions' => (clone $submissionQuery)->count(),
            'submitted' => (clone $submissionQuery)->where('status', 'submitted')->count(),
            'in_progress' => (clone $submissionQuery)->whereIn('status', ['disposed', 'assigned'])->count(),
            'in_analysis' => (clone $assignmentQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $submissionQuery)->where('status', 'completed')->count(),
            'total_assignments' => (clone $assignmentQuery)->count(),
        ];

        $recentSubmissions = $submissionQuery->with('submitter')->latest()->limit(6)->get();

        $bottleneck = [
            'Permohonan Masuk' => (clone $submissionQuery)->count(),
            'Sudah Divalidasi' => (clone $submissionQuery)->whereIn('status', ['accepted', 'disposed', 'assigned', 'completed'])->count(),
            'Sudah Disposisi' => (clone $submissionQuery)->whereIn('status', ['disposed', 'assigned', 'completed'])->count(),
            'Sedang Dianalisis' => (clone $assignmentQuery)->where('status', 'in_progress')->count(),
            'Selesai' => (clone $submissionQuery)->where('status', 'completed')->count(),
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
        ]);
    }
}
