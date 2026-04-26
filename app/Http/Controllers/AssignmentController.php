<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentAnalysisApproval;
use App\Models\AssignmentDocument;
use App\Models\AssignmentKemenkumReplyDocument;
use App\Models\AssignmentPicUpdate;
use App\Models\Submission;
use App\Models\User;
use App\Services\WorkflowNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly WorkflowNotificationService $workflowNotificationService
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role->value;

        abort_unless(in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true), 403);

        $query = Assignment::query()
            ->with(['submission.submitter.instansi', 'latestPicUpdate.analyst'])
            ->latest();
        $status = trim((string) $request->string('status'));
        $search = trim((string) $request->string('q'));
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;
        $allowedStatuses = ['assigned', 'in_progress', 'pending_kadiv_approval', 'pending_kakanwil_approval', 'revision_by_pic', 'completed'];

        if ($role === 'analis_hukum') {
            $query->whereAnalyst($user->id);
        }

        if (in_array($status, $allowedStatuses, true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $matchedStatuses = $this->matchAssignmentStatusesFromKeyword($search);
            $query->where(function ($builder) use ($search, $matchedStatuses): void {
                $builder
                    ->whereHas('submission', function ($submissionQuery) use ($search): void {
                        $submissionQuery
                            ->where('nomor_surat', 'like', "%{$search}%")
                            ->orWhere('perihal', 'like', "%{$search}%")
                            ->orWhere('perda_title', 'like', "%{$search}%")
                            ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') like ?", ["%{$search}%"])
                            ->orWhereHas('submitter.instansi', function ($instansiQuery) use ($search): void {
                                $instansiQuery->where('nama_instansi', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('latestPicUpdate.analyst', function ($analystQuery) use ($search): void {
                        $analystQuery->where('name', 'like', "%{$search}%");
                    });

                if ($matchedStatuses !== []) {
                    $builder->orWhereIn('status', $matchedStatuses);
                }
            });
        }

        return view('pages.assignments.index', [
            'assignments' => $query->paginate($perPage)->withQueryString(),
            'analysts' => User::query()->where('role', 'analis_hukum')->orderBy('name')->get(),
            'status' => $status,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function show(Request $request, Assignment $assignment)
    {
        $role = $request->user()->role->value;
        abort_unless(in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true), 403);

        $assignment->load([
            'assignedBy',
            'latestPicUpdate.analyst',
            'latestPicUpdate.picAssignedBy',
            'documents',
            'submission.submitter.instansi',
            'submission.latestStatus',
            'submission.latestDisposition.toUser',
            'submission.dispositions.toUser',
            'submission.documents',
        ]);

        if ($role === 'analis_hukum') {
            abort_unless($assignment->analyst_id === $request->user()->id, 403);
        }

        return view('pages.assignments.show', [
            'assignment' => $assignment,
        ]);
    }

    public function analysisResults(Request $request)
    {
        abort_unless(in_array($request->user()->role->value, ['analis_hukum', 'ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'operator_pemda'], true), 403);
        $search = trim((string) $request->string('q'));
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;

        $resultsQuery = Assignment::query()
            ->with(['submission.submitter.instansi', 'submission.documents', 'latestPicUpdate.analyst', 'latestAnalysisDocument'])
            ->where('status', 'completed')
            ->latest('updated_at');

        if ($request->user()->role->value === 'analis_hukum') {
            $resultsQuery->whereAnalyst($request->user()->id);
        } elseif ($request->user()->role->value === 'operator_pemda') {
            $resultsQuery->whereHas('submission', function ($query) use ($request) {
                $query->where('submitter_id', $request->user()->id);
            });
        }

        if ($search !== '') {
            $matchedStatuses = $this->matchAssignmentStatusesFromKeyword($search);
            $searchYear = preg_match('/^\d{4}$/', $search) === 1 ? (int) $search : null;

            $resultsQuery->where(function ($query) use ($search, $matchedStatuses, $searchYear): void {
                $query
                    ->whereHas('submission', function ($submissionQuery) use ($search): void {
                        $submissionQuery
                            ->where('nomor_surat', 'like', "%{$search}%")
                            ->orWhere('perihal', 'like', "%{$search}%")
                            ->orWhere('perda_title', 'like', "%{$search}%")
                            ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') like ?", ["%{$search}%"])
                            ->orWhereHas('submitter.instansi', function ($instansiQuery) use ($search): void {
                                $instansiQuery->where('nama_instansi', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('latestPicUpdate.analyst', function ($analystQuery) use ($search): void {
                        $analystQuery->where('name', 'like', "%{$search}%");
                    });

                if ($searchYear !== null) {
                    $query->orWhereHas('latestApproval', function ($approvalQuery) use ($searchYear): void {
                        $approvalQuery->whereYear('approved_by_kakanwil_at', $searchYear);
                    });
                }

                if ($matchedStatuses !== []) {
                    $query->orWhereIn('status', $matchedStatuses);
                }
            });
        }

        return view('pages.assignments.hasil-analisis', [
            'results' => $resultsQuery->paginate($perPage)->withQueryString(),
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }

    public function showAnalysisResult(Request $request, Assignment $assignment)
    {
        abort_unless(in_array($request->user()->role->value, ['analis_hukum', 'ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'operator_pemda'], true), 403);
        abort_unless($assignment->status->value === 'completed', 404);

        $assignment->load(['submission.submitter.instansi', 'latestPicUpdate.analyst', 'assignedBy', 'documents']);

        $user = $request->user();
        if ($user->role->value === 'analis_hukum') {
            abort_unless($assignment->analyst_id === $user->id, 403);
        }

        if ($user->role->value === 'operator_pemda') {
            abort_unless($assignment->submission?->submitter_id === $user->id, 403);
        }

        $latestAnalysisDocument = $assignment->documents
            ->where('document_type', 'hasil_analisis')
            ->sortByDesc('id')
            ->first();

        return view('pages.assignments.show-hasil-analisis', [
            'assignment' => $assignment,
            'latestAnalysisDocument' => $latestAnalysisDocument,
            'analysisFields' => $this->extractAnalysisFieldsFromDocument($latestAnalysisDocument),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(in_array($request->user()->role->value, ['kakanwil', 'kepala_divisi_p3h'], true), 403);

        $validated = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'instruction' => ['nullable', 'string'],
        ]);

        $assignment = Assignment::query()->create([
            'submission_id' => $validated['submission_id'],
            'assigned_by_id' => $request->user()->id,
            'instruction' => $validated['instruction'] ?? null,
            'status' => 'assigned',
        ]);

        Submission::query()->whereKey($validated['submission_id'])->each(function (Submission $submission) use ($request): void {
            $submission->recordStatus('assigned', $request->user()->id);
        });

        $this->workflowNotificationService->notifyAssignmentCreated($assignment, $request->user());

        return back()->with('success', 'Penugasan berhasil dibuat. Status: Belum ada Penanggung Jawab.');
    }

    public function createFromSubmission(Request $request, Submission $submission)
    {
        abort_unless(in_array($request->user()->role->value, ['kakanwil', 'kepala_divisi_p3h'], true), 403);

        return view('pages.submissions.penugasan', [
            'submission' => $submission,
        ]);
    }

    public function storeFromSubmission(Request $request, Submission $submission)
    {
        abort_unless(in_array($request->user()->role->value, ['kakanwil', 'kepala_divisi_p3h'], true), 403);

        $validated = $request->validate([
            'instruction' => ['nullable', 'string'],
        ]);

        $assignment = Assignment::query()->create([
            'submission_id' => $submission->id,
            'assigned_by_id' => $request->user()->id,
            'instruction' => $validated['instruction'] ?? null,
            'status' => 'assigned',
        ]);

        $submission->recordStatus('assigned', $request->user()->id);

        $this->workflowNotificationService->notifyAssignmentCreated($assignment, $request->user());

        return redirect()->route('submissions.index')->with('success', 'Penugasan berhasil dibuat. Status: Belum ada Penanggung Jawab.');
    }

    public function assignPicForm(Request $request, Assignment $assignment)
    {
        abort_unless($request->user()->role->value === 'ketua_tim_analisis', 403);
        abort_unless($assignment->status->value === 'assigned', 422);

        $assignment->load(['submission']);

        return view('pages.assignments.assign-pic', [
            'assignment' => $assignment,
            'analysts' => User::query()->where('role', 'analis_hukum')->orderBy('name')->get(),
        ]);
    }

    public function assignPicStore(Request $request, Assignment $assignment)
    {
        abort_unless($request->user()->role->value === 'ketua_tim_analisis', 403);
        abort_unless($assignment->status->value === 'assigned', 422);

        $validated = $request->validate([
            'analyst_id' => ['required', 'exists:users,id'],
            'deadline_at' => ['nullable', 'date'],
            'surat_balasan_kemenkum' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
        ]);

        $analyst = User::query()->findOrFail($validated['analyst_id']);
        abort_unless($analyst->role->value === 'analis_hukum', 422);

        $file = $this->validateUploadedFile(
            $request->file('surat_balasan_kemenkum'),
            'surat_balasan_kemenkum',
            'Upload surat balasan Kemenkum gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeAssignmentFile(
            $file,
            $assignment->submission?->submitter?->instansi?->nama_instansi ?? $assignment->submission?->pemda_name ?? 'Instansi',
            'Surat Balasan Kemenkum'
        );

        DB::transaction(function () use ($request, $assignment, $analyst, $validated, $stored): void {
            $assignment->update([
                'status' => 'in_progress',
            ]);

            AssignmentPicUpdate::query()->create([
                'assignment_id' => $assignment->id,
                'pic_assigned_by_id' => $request->user()->id,
                'analyst_id' => $analyst->id,
                'deadline_at' => $validated['deadline_at'] ?? null,
            ]);

            AssignmentKemenkumReplyDocument::query()->updateOrCreate(
                ['assignment_id' => $assignment->id],
                [
                    'uploaded_by' => $request->user()->id,
                    'file_name' => $stored['file_name'],
                    'file_path' => $stored['file_path'],
                    'mime_type' => $stored['mime_type'],
                    'file_size' => $stored['file_size'],
                ]
            );
        });

        $this->workflowNotificationService->notifyAssignmentPicAssigned(
            $assignment,
            $request->user(),
            $analyst,
            $validated['deadline_at'] ?? null
        );

        $this->workflowNotificationService->notifySubmitterReplyLetterAvailable(
            $assignment,
            $request->user()
        );

        return redirect()->route('assignments.index')->with('success', 'Penanggung Jawab berhasil ditentukan. Status menjadi Dalam Analisis.');
    }

    public function uploadAnalysisForm(Request $request, Assignment $assignment)
    {
        abort_unless(
            $request->user()->role->value === 'analis_hukum' &&
            $assignment->analyst_id === $request->user()->id &&
            in_array($assignment->status->value, ['in_progress', 'revision_by_pic'], true),
            403
        );

        $assignment->load(['submission', 'latestAnalysisDocument']);
        $initialAnalysis = $this->extractAnalysisFieldsFromDocument($assignment->latestAnalysisDocument);

        return view('pages.assignments.upload-hasil', [
            'assignment' => $assignment,
            'initialAnalysis' => $initialAnalysis,
        ]);
    }

    public function editAnalysisResultForm(Request $request, Assignment $assignment)
    {
        abort_unless(
            $request->user()->role->value === 'analis_hukum' &&
            $assignment->analyst_id === $request->user()->id &&
            in_array($assignment->status->value, ['in_progress', 'revision_by_pic'], true),
            403
        );

        $assignment->load(['submission', 'latestAnalysisDocument']);
        $initialAnalysis = $this->extractAnalysisFieldsFromDocument($assignment->latestAnalysisDocument);

        return view('pages.assignments.edit-hasil-analisis', [
            'assignment' => $assignment,
            'initialAnalysis' => $initialAnalysis,
        ]);
    }

    public function uploadAnalysisStore(Request $request, Assignment $assignment)
    {
        abort_unless(
            $request->user()->role->value === 'analis_hukum' &&
            $assignment->analyst_id === $request->user()->id &&
            in_array($assignment->status->value, ['in_progress', 'revision_by_pic'], true),
            403
        );

        $validated = $request->validate([
            'ringkasan_analisis' => ['required', 'string'],
            'hasil_evaluasi' => ['required', 'string'],
            'rekomendasi' => ['required', 'string'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
        ]);

        $file = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Upload hasil analisis gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeAssignmentFile(
            $file,
            $assignment->submission?->submitter?->instansi?->nama_instansi ?? $assignment->submission?->pemda_name ?? 'Instansi',
            'Hasil Analisis'
        );

        DB::transaction(function () use ($request, $assignment, $validated, $stored): void {
            AssignmentDocument::query()->create([
                'assignment_id' => $assignment->id,
                'uploaded_by' => $request->user()->id,
                'document_type' => 'hasil_analisis',
                'file_name' => $stored['file_name'],
                'file_path' => $stored['file_path'],
                'mime_type' => $stored['mime_type'],
                'file_size' => $stored['file_size'],
                'ringkasan_analisis' => $validated['ringkasan_analisis'],
                'hasil_evaluasi' => $validated['hasil_evaluasi'],
                'rekomendasi' => $validated['rekomendasi'],
            ]);

            $assignment->update([
                'status' => 'pending_kadiv_approval',
            ]);
        });

        $this->workflowNotificationService->notifyAssignmentSubmittedForKadivReview($assignment, $request->user());

        return redirect()->route('assignments.index')->with('success', 'Hasil analisis berhasil diunggah. Status: Menunggu Persetujuan Kadiv.');
    }

    public function approvalForm(Request $request, Assignment $assignment)
    {
        $role = $request->user()->role->value;
        abort_unless(in_array($role, ['kepala_divisi_p3h', 'kakanwil'], true), 403);
        abort_unless($this->canReviewAssignmentByRole($role, $assignment), 422);

        $assignment->load(['submission', 'latestPicUpdate.analyst', 'assignedBy']);

        return view('pages.assignments.approval', [
            'assignment' => $assignment,
            'reviewRole' => $role,
        ]);
    }

    public function approvalStore(Request $request, Assignment $assignment)
    {
        $role = $request->user()->role->value;
        abort_unless(in_array($role, ['kepala_divisi_p3h', 'kakanwil'], true), 403);
        abort_unless($this->canReviewAssignmentByRole($role, $assignment), 422);

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'revise'])],
            'revision_note' => ['nullable', 'string', 'required_if:decision,revise'],
        ]);

        if ($role === 'kepala_divisi_p3h') {
            if ($validated['decision'] === 'approve') {
                $assignment->update([
                    'status' => 'pending_kakanwil_approval',
                ]);

                AssignmentAnalysisApproval::query()->create([
                    'assignment_id' => $assignment->id,
                    'assigned_by_id' => $request->user()->id,
                    'revision_note' => null,
                    'approved_by_kadiv_at' => now(),
                    'approved_by_kakanwil_at' => null,
                ]);

                $this->workflowNotificationService->notifyAssignmentForwardedToKakanwil($assignment, $request->user());

                return redirect()->route('assignments.index')->with('success', 'Persetujuan Kadiv berhasil. Status: Menunggu Persetujuan Kakanwil.');
            }

            $assignment->update([
                'status' => 'revision_by_pic',
            ]);

            AssignmentAnalysisApproval::query()->create([
                'assignment_id' => $assignment->id,
                'assigned_by_id' => $request->user()->id,
                'revision_note' => $validated['revision_note'],
                'approved_by_kadiv_at' => null,
                'approved_by_kakanwil_at' => null,
            ]);

            $this->workflowNotificationService->notifyAssignmentReturnedForRevision(
                $assignment,
                $request->user(),
                $validated['revision_note'] ?? null
            );

            return redirect()->route('assignments.index')->with('success', 'Penugasan dikembalikan untuk revisi Penanggung Jawab.');
        }

        if ($validated['decision'] === 'approve') {
            $approverId = $request->user()->id;

            DB::transaction(function () use ($assignment, $approverId): void {
                $lastKadivApprovalAt = $assignment->approved_by_kadiv_at;

                $assignment->update([
                    'status' => 'completed',
                ]);

                AssignmentAnalysisApproval::query()->create([
                    'assignment_id' => $assignment->id,
                    'assigned_by_id' => $approverId,
                    'revision_note' => null,
                    'approved_by_kadiv_at' => $lastKadivApprovalAt,
                    'approved_by_kakanwil_at' => now(),
                ]);

                $assignment->submission?->recordStatus('completed', $approverId);
            });

            $this->workflowNotificationService->notifyAssignmentCompleted($assignment, $request->user());

            return redirect()->route('assignments.index')->with('success', 'Persetujuan Kakanwil berhasil. Status: Selesai Analisis.');
        }

        $assignment->update([
            'status' => 'revision_by_pic',
        ]);

        AssignmentAnalysisApproval::query()->create([
            'assignment_id' => $assignment->id,
            'assigned_by_id' => $request->user()->id,
            'revision_note' => $validated['revision_note'],
            'approved_by_kadiv_at' => $assignment->approved_by_kadiv_at,
            'approved_by_kakanwil_at' => null,
        ]);

        $this->workflowNotificationService->notifyAssignmentReturnedForRevision(
            $assignment,
            $request->user(),
            $validated['revision_note'] ?? null
        );

        return redirect()->route('assignments.index')->with('success', 'Penugasan dikembalikan untuk revisi Penanggung Jawab.');
    }

    public function uploadDocument(Request $request, Assignment $assignment)
    {
        abort_unless(
            ($request->user()->role->value === 'analis_hukum' && $assignment->analyst_id === $request->user()->id) ||
            $request->user()->role->value === 'ketua_tim_analisis',
            403
        );

        $validated = $request->validate([
            'document_type' => ['required', Rule::in(['hasil_analisis', 'rekomendasi', 'lampiran'])],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
        ]);

        $file = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Upload dokumen penugasan gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeAssignmentFile(
            $file,
            $assignment->submission?->submitter?->instansi?->nama_instansi ?? $assignment->submission?->pemda_name ?? 'Instansi',
            $validated['document_type'] === 'hasil_analisis' ? 'Hasil Analisis' : 'Dokumen'
        );

        AssignmentDocument::query()->create([
            'assignment_id' => $assignment->id,
            'uploaded_by' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'file_name' => $stored['file_name'],
            'file_path' => $stored['file_path'],
            'mime_type' => $stored['mime_type'],
            'file_size' => $stored['file_size'],
        ]);

        return back()->with('success', 'Dokumen penugasan berhasil diunggah.');
    }

    private function canReviewAssignmentByRole(string $role, Assignment $assignment): bool
    {
        if ($role === 'kepala_divisi_p3h') {
            return $assignment->status->value === 'pending_kadiv_approval';
        }

        if ($role === 'kakanwil') {
            return $assignment->status->value === 'pending_kakanwil_approval';
        }

        return false;
    }

    private function validateUploadedFile(
        mixed $file,
        string $field,
        string $message
    ): UploadedFile {
        if (! $file instanceof UploadedFile || ! $file->isValid() || blank($file->getRealPath())) {
            throw ValidationException::withMessages([$field => $message]);
        }

        return $file;
    }

    /**
     * @return array{file_name:string,file_path:string,mime_type:?string,file_size:int|false}
     */
    private function storeAssignmentFile(UploadedFile $file, string $instansiName, string $documentLabel): array
    {
        $destinationPath = public_path('storage/penugasan');

        if (! is_dir($destinationPath) && ! mkdir($destinationPath, 0755, true) && ! is_dir($destinationPath)) {
            throw ValidationException::withMessages([
                'file' => 'Folder upload penugasan tidak dapat dibuat.',
            ]);
        }

        $displayName = $this->buildDisplayDocumentName($instansiName, $documentLabel, now());
        $extension = $file->getClientOriginalExtension();
        $storedName = $displayName.($extension ? '.'.$extension : '');
        if (file_exists($destinationPath.DIRECTORY_SEPARATOR.$storedName)) {
            $storedName = $displayName.'_'.Str::lower(Str::random(4)).($extension ? '.'.$extension : '');
        }
        $fileSize = $file->getSize();
        $mimeType = $file->getClientMimeType();

        $file->move($destinationPath, $storedName);

        return [
            'file_name' => $displayName,
            'file_path' => 'penugasan/'.$storedName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ];
    }

    private function buildDisplayDocumentName(string $instansiName, string $documentLabel, \Illuminate\Support\Carbon $timestamp): string
    {
        $normalize = function (string $value): string {
            $parts = preg_split('/[^A-Za-z0-9]+/', trim($value)) ?: [];
            $parts = array_filter($parts, static fn ($part) => $part !== '');

            return $parts === [] ? 'Dokumen' : implode('', $parts);
        };

        return $normalize($instansiName).'_'.$normalize($documentLabel).'_'.$timestamp->format('YmdHis');
    }

    private function extractAnalysisFieldsFromDocument(?AssignmentDocument $document): array
    {
        $result = [
            'ringkasan_analisis' => '',
            'hasil_evaluasi' => '',
            'rekomendasi' => '',
        ];

        if (! $document) {
            return $result;
        }

        $result['ringkasan_analisis'] = trim((string) ($document->ringkasan_analisis ?? ''));
        $result['hasil_evaluasi'] = trim((string) ($document->hasil_evaluasi ?? ''));
        $result['rekomendasi'] = trim((string) ($document->rekomendasi ?? ''));

        return $result;
    }

    /**
     * @return array<int, string>
     */
    private function matchAssignmentStatusesFromKeyword(string $search): array
    {
        $normalized = $this->normalizeSearchTerm($search);
        if ($normalized === '') {
            return [];
        }

        $keywords = [
            'assigned' => ['assigned', 'belum ada penanggung jawab', 'tanpa penanggung jawab'],
            'in_progress' => ['in progress', 'in_progress', 'dalam analisis', 'sedang dianalisis'],
            'pending_kadiv_approval' => ['pending kadiv', 'menunggu persetujuan kadiv', 'menunggu kadiv'],
            'pending_kakanwil_approval' => ['pending kakanwil', 'menunggu persetujuan kakanwil', 'menunggu kakanwil'],
            'revision_by_pic' => ['revision', 'revisi', 'revisi oleh penanggung jawab', 'revisi oleh pic'],
            'completed' => ['completed', 'selesai', 'selesai analisis'],
        ];

        $matched = [];
        foreach ($keywords as $status => $aliases) {
            foreach ($aliases as $alias) {
                $normalizedAlias = $this->normalizeSearchTerm($alias);
                if (
                    $normalizedAlias !== '' &&
                    (str_contains($normalized, $normalizedAlias) || str_contains($normalizedAlias, $normalized))
                ) {
                    $matched[] = $status;
                    break;
                }
            }
        }

        return $matched;
    }

    private function normalizeSearchTerm(string $value): string
    {
        $normalized = Str::of($value)->lower()->ascii()->squish()->value();

        return trim($normalized);
    }
}
