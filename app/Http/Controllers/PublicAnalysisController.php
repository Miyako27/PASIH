<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('q'));
        $instansiId = (int) $request->integer('instansi_id', 0);
        $year = trim((string) $request->string('year'));
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;

        $query = Assignment::query()
            ->with([
                'submission.submitter.instansi',
                'submission.documents',
                'documents',
                'latestApproval',
            ])
            ->where('status', 'completed')
            ->latest('updated_at');

        if ($search !== '') {
            $normalizedSearch = trim(Str::of($search)->lower()->ascii()->squish()->value());
            $searchYear = preg_match('/^\d{4}$/', $search) === 1 ? (int) $search : null;
            $isCompletedKeyword = str_contains($normalizedSearch, 'selesai analisis') || str_contains($normalizedSearch, 'completed') || str_contains($normalizedSearch, 'selesai');

            $query->where(function ($builder) use ($search, $searchYear, $isCompletedKeyword): void {
                $builder->whereHas('submission', function ($submissionQuery) use ($search): void {
                    $submissionQuery
                        ->where('perda_title', 'like', "%{$search}%")
                        ->orWhere('nomor_surat', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%")
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') like ?", ["%{$search}%"])
                        ->orWhereHas('submitter.instansi', function ($instansiQuery) use ($search): void {
                            $instansiQuery->where('nama_instansi', 'like', "%{$search}%");
                        });
                });

                if ($searchYear !== null) {
                    $builder->orWhereHas('latestApproval', function ($approvalQuery) use ($searchYear): void {
                        $approvalQuery->whereYear('approved_by_kakanwil_at', $searchYear);
                    });
                }

                if ($isCompletedKeyword) {
                    $builder->orWhere('status', 'completed');
                }
            });
        }

        if ($instansiId > 0) {
            $query->whereHas('submission.submitter.instansi', function ($builder) use ($instansiId): void {
                $builder->where('instansi.id_instansi', $instansiId);
            });
        }

        if ($year !== '' && preg_match('/^\\d{4}$/', $year) === 1) {
            $query->whereHas('analysisApprovals', function ($builder) use ($year): void {
                $builder->whereYear('approved_by_kakanwil_at', (int) $year);
            });
        }

        $results = $query->paginate($perPage)->withQueryString();

        $years = Assignment::query()
            ->where('status', 'completed')
            ->whereHas('analysisApprovals', function ($builder): void {
                $builder->whereNotNull('approved_by_kakanwil_at');
            })
            ->with('latestApproval')
            ->get()
            ->map(fn (Assignment $assignment) => optional($assignment->completed_at)->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('public.analysis.index', [
            'results' => $results,
            'search' => $search,
            'instansiId' => $instansiId,
            'year' => $year,
            'perPage' => $perPage,
            'instansiOptions' => Instansi::query()->orderBy('nama_instansi')->get(['id_instansi', 'nama_instansi']),
            'years' => $years,
        ]);
    }

    public function show(Assignment $assignment)
    {
        abort_unless($assignment->status->value === 'completed', 404);

        $assignment->load([
            'submission.submitter.instansi',
            'submission.documents',
            'documents',
            'latestApproval',
        ]);

        $analysisDocuments = $assignment->documents
            ->where('document_type', 'hasil_analisis')
            ->sortByDesc('id')
            ->values();

        $latestAnalysisDocument = $analysisDocuments->first();
        $perdaDocument = $assignment->submission?->documents
            ->where('document_type', 'peraturan_daerah')
            ->sortByDesc('id')
            ->first();

        return view('public.analysis.show', [
            'assignment' => $assignment,
            'latestAnalysisDocument' => $latestAnalysisDocument,
            'perdaDocument' => $perdaDocument,
        ]);
    }
}
