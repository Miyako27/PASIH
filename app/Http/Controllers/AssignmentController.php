<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentDocument;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role->value;

        abort_unless(in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true), 403);

        $query = Assignment::query()
            ->with(['submission', 'analyst'])
            ->latest();

        if ($role === 'analis_hukum') {
            $query->where('analyst_id', $user->id);
        }

        return view('pages.assignments.index', [
            'assignments' => $query->paginate(10),
            'analysts' => User::query()->where('role', 'analis_hukum')->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Assignment $assignment)
    {
        $role = $request->user()->role->value;
        abort_unless(in_array($role, ['ketua_tim_analisis', 'kakanwil', 'kepala_divisi_p3h', 'analis_hukum'], true), 403);

        $assignment->load([
            'assignedBy',
            'picAssignedBy',
            'analyst',
            'documents',
            'submission.submitter',
            'submission.divisionOperator',
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
        abort_unless(in_array($request->user()->role->value, ['analis_hukum', 'ketua_tim_analisis', 'operator_pemda'], true), 403);

        $resultsQuery = Assignment::query()
            ->with(['submission', 'analyst', 'latestAnalysisDocument'])
            ->where('status', 'completed')
            ->latest('completed_at');

        if ($request->user()->role->value === 'analis_hukum') {
            $resultsQuery->where('analyst_id', $request->user()->id);
        } elseif ($request->user()->role->value === 'operator_pemda') {
            $resultsQuery->whereHas('submission', function ($query) use ($request) {
                $query->where('submitter_id', $request->user()->id);
            });
        }

        return view('pages.assignments.hasil-analisis', [
            'results' => $resultsQuery->paginate(5),
        ]);
    }

    public function showAnalysisResult(Request $request, Assignment $assignment)
    {
        abort_unless(in_array($request->user()->role->value, ['analis_hukum', 'ketua_tim_analisis', 'operator_pemda'], true), 403);
        abort_unless($assignment->status->value === 'completed', 404);

        $assignment->load(['submission.submitter', 'analyst', 'assignedBy', 'documents']);

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
            'analysisFields' => $this->extractAnalysisFieldsFromNotes($latestAnalysisDocument?->notes),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(in_array($request->user()->role->value, ['kakanwil', 'kepala_divisi_p3h'], true), 403);

        $validated = $request->validate([
            'submission_id' => ['required', 'exists:submissions,id'],
            'instruction' => ['nullable', 'string'],
            'deadline_at' => ['nullable', 'date'],
        ]);

        Assignment::query()->create([
            'submission_id' => $validated['submission_id'],
            'assigned_by_id' => $request->user()->id,
            'analyst_id' => null,
            'instruction' => $validated['instruction'] ?? null,
            'deadline_at' => $validated['deadline_at'] ?? null,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        Submission::query()->whereKey($validated['submission_id'])->update([
            'assigned_by_id' => $request->user()->id,
        ]);

        return back()->with('success', 'Penugasan berhasil dibuat. Status: Belum ada PIC.');
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
            'deadline_at' => ['nullable', 'date'],
        ]);

        Assignment::query()->create([
            'submission_id' => $submission->id,
            'assigned_by_id' => $request->user()->id,
            'analyst_id' => null,
            'instruction' => $validated['instruction'] ?? null,
            'deadline_at' => $validated['deadline_at'] ?? null,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $submission->update([
            'assigned_by_id' => $request->user()->id,
        ]);

        return redirect()->route('submissions.index')->with('success', 'Penugasan berhasil dibuat. Status: Belum ada PIC.');
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
        ]);

        $analyst = User::query()->findOrFail($validated['analyst_id']);
        abort_unless($analyst->role->value === 'analis_hukum', 422);

        $assignment->update([
            'analyst_id' => $analyst->id,
            'pic_assigned_by_id' => $request->user()->id,
            'pic_assigned_at' => now(),
            'status' => 'in_progress',
            'started_at' => $assignment->started_at ?? now(),
            'revision_note' => null,
        ]);

        return redirect()->route('assignments.index')->with('success', 'PIC berhasil ditentukan. Status menjadi Dalam Analisis.');
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
        $initialAnalysis = $this->extractAnalysisFieldsFromNotes($assignment->latestAnalysisDocument?->notes);

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
        $initialAnalysis = $this->extractAnalysisFieldsFromNotes($assignment->latestAnalysisDocument?->notes);

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
        $stored = $this->storeAssignmentFile($file);

        DB::transaction(function () use ($request, $assignment, $validated, $stored): void {
            AssignmentDocument::query()->create([
                'assignment_id' => $assignment->id,
                'uploaded_by' => $request->user()->id,
                'document_type' => 'hasil_analisis',
                'file_name' => $stored['file_name'],
                'file_path' => $stored['file_path'],
                'mime_type' => $stored['mime_type'],
                'file_size' => $stored['file_size'],
                'notes' => "Ringkasan: {$validated['ringkasan_analisis']}\n\nHasil Evaluasi: {$validated['hasil_evaluasi']}\n\nRekomendasi: {$validated['rekomendasi']}",
            ]);

            $assignment->update([
                'status' => 'pending_kadiv_approval',
                'started_at' => $assignment->started_at ?? now(),
                'submitted_for_review_at' => now(),
                'approved_by_kadiv_at' => null,
                'approved_by_kakanwil_at' => null,
                'revision_note' => null,
                'completed_at' => null,
            ]);
        });

        return redirect()->route('assignments.index')->with('success', 'Hasil analisis berhasil diunggah. Status: Menunggu ACC Kadiv.');
    }

    public function approvalForm(Request $request, Assignment $assignment)
    {
        $role = $request->user()->role->value;
        abort_unless(in_array($role, ['kepala_divisi_p3h', 'kakanwil'], true), 403);
        abort_unless($this->canReviewAssignmentByRole($role, $assignment), 422);

        $assignment->load(['submission', 'analyst', 'assignedBy']);

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
                    'approved_by_kadiv_at' => now(),
                    'revision_note' => null,
                ]);

                return redirect()->route('assignments.index')->with('success', 'ACC Kadiv berhasil. Status: Menunggu ACC Kakanwil.');
            }

            $assignment->update([
                'status' => 'revision_by_pic',
                'revision_note' => $validated['revision_note'],
                'completed_at' => null,
            ]);

            return redirect()->route('assignments.index')->with('success', 'Penugasan dikembalikan untuk revisi PIC.');
        }

        if ($validated['decision'] === 'approve') {
            DB::transaction(function () use ($assignment): void {
                $assignment->update([
                    'status' => 'completed',
                    'approved_by_kakanwil_at' => now(),
                    'revision_note' => null,
                    'completed_at' => now(),
                ]);

                $assignment->submission()->update([
                    'status' => 'completed',
                    'finished_at' => now(),
                ]);
            });

            return redirect()->route('assignments.index')->with('success', 'ACC Kakanwil berhasil. Status: Selesai Analisis.');
        }

        $assignment->update([
            'status' => 'revision_by_pic',
            'revision_note' => $validated['revision_note'],
            'approved_by_kakanwil_at' => null,
            'completed_at' => null,
        ]);

        return redirect()->route('assignments.index')->with('success', 'Penugasan dikembalikan untuk revisi PIC.');
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
            'notes' => ['nullable', 'string'],
        ]);

        $file = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Upload dokumen penugasan gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeAssignmentFile($file);

        AssignmentDocument::query()->create([
            'assignment_id' => $assignment->id,
            'uploaded_by' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'file_name' => $stored['file_name'],
            'file_path' => $stored['file_path'],
            'mime_type' => $stored['mime_type'],
            'file_size' => $stored['file_size'],
            'notes' => $validated['notes'] ?? null,
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
    private function storeAssignmentFile(UploadedFile $file): array
    {
        $destinationPath = public_path('storage/penugasan');

        if (! is_dir($destinationPath) && ! mkdir($destinationPath, 0755, true) && ! is_dir($destinationPath)) {
            throw ValidationException::withMessages([
                'file' => 'Folder upload penugasan tidak dapat dibuat.',
            ]);
        }

        $originalName = $file->getClientOriginalName();
        $storedName = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);
        $fileSize = $file->getSize();
        $mimeType = $file->getClientMimeType();

        $file->move($destinationPath, $storedName);

        return [
            'file_name' => $originalName,
            'file_path' => 'penugasan/'.$storedName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ];
    }

    /**
     * Convert stored notes format into form-friendly values.
     *
     * Stored format:
     * Ringkasan: ...
     *
     * Hasil Evaluasi: ...
     *
     * Rekomendasi: ...
     */
    private function extractAnalysisFieldsFromNotes(?string $notes): array
    {
        $result = [
            'ringkasan_analisis' => '',
            'hasil_evaluasi' => '',
            'rekomendasi' => '',
        ];

        if (blank($notes)) {
            return $result;
        }

        if (preg_match('/Ringkasan:\s*(.*?)\n\nHasil Evaluasi:/s', $notes, $m)) {
            $result['ringkasan_analisis'] = trim($m[1]);
        }

        if (preg_match('/Hasil Evaluasi:\s*(.*?)\n\nRekomendasi:/s', $notes, $m)) {
            $result['hasil_evaluasi'] = trim($m[1]);
        }

        if (preg_match('/Rekomendasi:\s*(.*)$/s', $notes, $m)) {
            $result['rekomendasi'] = trim($m[1]);
        }

        return $result;
    }
}
