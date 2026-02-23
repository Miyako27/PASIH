<?php

namespace App\Http\Controllers;

use App\Models\Disposition;
use App\Models\Submission;
use App\Models\SubmissionDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless(in_array($user->role->value, ['operator_pemda', 'operator_kanwil', 'kakanwil', 'kepala_divisi_p3h'], true), 403);
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;
        $search = trim((string) $request->string('q'));

        $query = Submission::query()->with(['submitter', 'divisionOperator', 'latestDisposition.toUser', 'assignments.analyst'])->latest();

        if ($user->role->value === 'operator_pemda') {
            $query->where('submitter_id', $user->id);
        }

        if (in_array($user->role->value, ['kakanwil', 'kepala_divisi_p3h'], true)) {
            $query->whereIn('status', ['disposed', 'assigned', 'completed', 'accepted']);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('nomor_surat', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%")
                    ->orWhere('pemda_name', 'like', "%{$search}%")
                    ->orWhere('perda_title', 'like', "%{$search}%");
            });
        }

        return view('pages.submissions.index', [
            'submissions' => $query->paginate($perPage)->withQueryString(),
            'canCreate' => $user->role->value === 'operator_pemda',
            'canReview' => $user->role->value === 'operator_kanwil',
            'canUploadResult' => $user->role->value === 'analis_hukum',
            'divisionUsers' => User::query()->where('role', 'kepala_divisi_p3h')->get(),
            'canAssignFromSubmission' => in_array($user->role->value, ['kakanwil', 'kepala_divisi_p3h'], true),
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->role->value === 'operator_pemda', 403);

        return view('pages.submissions.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->role->value === 'operator_pemda', 403);

        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'surat_permohonan' => ['required', 'array'],
            'surat_permohonan.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp'],
        ]);

        DB::transaction(function () use ($request, $validated): void {

            $submission = Submission::query()->create([
                'submitter_id' => $request->user()->id,
                'nomor_surat' => $validated['nomor_surat'],
                'perihal' => $validated['perihal'],
                'pemda_name' => $request->user()->name,
                'perda_title' => $validated['perihal'],
                'description' => $validated['description'] ?? null,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($request->file('surat_permohonan') as $file) {

                $validatedFile = $this->validateUploadedFile(
                    $file,
                    'surat_permohonan',
                    'Upload dokumen gagal. Pastikan ukuran file tidak melebihi batas server.'
                );

                $this->storeDocument(
                    $submission->id,
                    $request->user()->id,
                    $validatedFile,
                    'surat_permohonan'
                );
            }
        });

        return redirect()->route('submissions.index')->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function show(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value !== 'analis_hukum', 403);
        $this->authorizeView($request, $submission);

        $submission->load([
            'submitter',
            'documents',
            'dispositions.toUser',
            'latestDisposition.toUser',
            'assignments.analyst',
            'assignments.documents',
        ]);

        return view('pages.submissions.show', [
            'submission' => $submission,
            'canUploadResult' => $request->user()->role->value === 'analis_hukum',
        ]);
    }

    public function edit(Request $request, Submission $submission)
    {
        abort_unless(
            $request->user()->role->value === 'operator_pemda' &&
            $submission->submitter_id === $request->user()->id &&
            in_array($submission->status->value, ['submitted', 'revised'], true),
            403
        );

        return view('pages.submissions.edit', ['submission' => $submission]);
    }

    public function update(Request $request, Submission $submission)
    {
        abort_unless(
            $request->user()->role->value === 'operator_pemda' &&
            $submission->submitter_id === $request->user()->id,
            403
        );

        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'pemda_name' => ['required', 'string', 'max:255'],
            'perda_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'dokumen_pendukung' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,zip'],
        ]);

        $submission->update([
            'nomor_surat' => $validated['nomor_surat'],
            'perihal' => $validated['perihal'],
            'pemda_name' => $validated['pemda_name'],
            'perda_title' => $validated['perda_title'],
            'description' => $validated['description'] ?? null,
            'status' => 'submitted',
            'reviewed_at' => null,
            'revision_note' => null,
            'rejection_note' => null,
        ]);

        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPendukung = $this->validateUploadedFile(
                $request->file('dokumen_pendukung'),
                'dokumen_pendukung',
                'Upload dokumen pendukung gagal. Periksa ukuran file dan coba lagi.'
            );
            $this->storeDocument($submission->id, $request->user()->id, $dokumenPendukung, 'dokumen_pendukung');
        }

        return redirect()->route('submissions.show', $submission)->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Request $request, Submission $submission)
    {
        abort_unless(
            $request->user()->role->value === 'operator_pemda' &&
            $submission->submitter_id === $request->user()->id &&
            in_array($submission->status->value, ['submitted', 'revised'], true),
            403
        );

        $submission->delete();

        return redirect()->route('submissions.index')->with('success', 'Data permohonan berhasil dihapus.');
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value === 'operator_kanwil', 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'revised', 'rejected'])],
            'note' => ['nullable', 'string'],
        ]);

        $statusNote = blank($validated['note'] ?? null) ? null : $validated['note'];

        $submission->update([
            'kanwil_operator_id' => $request->user()->id,
            'status' => $validated['status'],
            'reviewed_at' => now(),
            // Gunakan revision_note sebagai catatan status umum (accepted/revised),
            // sementara rejected tetap memakai rejection_note.
            'revision_note' => in_array($validated['status'], ['accepted', 'revised'], true) ? $statusNote : null,
            'rejection_note' => $validated['status'] === 'rejected' ? $statusNote : null,
        ]);

        return back()->with('success', 'Status pengajuan diperbarui.');
    }

    public function statusDispositionForm(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value === 'operator_kanwil', 403);

        return view('pages.submissions.status-disposisi', [
            'submission' => $submission,
            'kadivUser' => User::query()
                ->where('role', 'kepala_divisi_p3h')
                ->orderBy('name')
                ->first(),
        ]);
    }

    public function saveStatusDisposition(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value === 'operator_kanwil', 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'revised', 'rejected'])],
            'to_user_id' => ['nullable', 'exists:users,id'],
            'status_note' => ['nullable', 'string'],
            'disposition_note' => ['nullable', 'string'],
        ]);

        $statusNote = blank($validated['status_note'] ?? null) ? null : $validated['status_note'];

        DB::transaction(function () use ($request, $submission, $validated, $statusNote): void {
            $submission->update([
                'kanwil_operator_id' => $request->user()->id,
                'status' => $validated['status'],
                'reviewed_at' => now(),
                // Gunakan revision_note sebagai catatan status umum (accepted/revised),
                // sementara rejected tetap memakai rejection_note.
                'revision_note' => in_array($validated['status'], ['accepted', 'revised'], true) ? $statusNote : null,
                'rejection_note' => $validated['status'] === 'rejected' ? $statusNote : null,
            ]);

            if (! empty($validated['to_user_id'])) {
                $toUser = User::query()->findOrFail($validated['to_user_id']);
                abort_unless($toUser->role->value === 'kepala_divisi_p3h', 422);

                Disposition::query()->create([
                    'submission_id' => $submission->id,
                    'from_user_id' => $request->user()->id,
                    'to_user_id' => $toUser->id,
                    'note' => $validated['disposition_note'] ?? null,
                    'disposed_at' => now(),
                ]);

                $submission->update([
                    'division_operator_id' => $toUser->id,
                ]);
            }
        });

        return redirect()
            ->route('submissions.index')
            ->with('success', 'Status dan disposisi permohonan berhasil disimpan.');
    }

    public function dispose(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value === 'operator_kanwil', 403);

        $validated = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'note' => ['nullable', 'string'],
        ]);

        $toUser = User::query()->findOrFail($validated['to_user_id']);
        abort_unless($toUser->role->value === 'kepala_divisi_p3h', 422);

        Disposition::query()->create([
            'submission_id' => $submission->id,
            'from_user_id' => $request->user()->id,
            'to_user_id' => $toUser->id,
            'note' => $validated['note'] ?? null,
            'disposed_at' => now(),
        ]);

        $submission->update([
            'status' => 'disposed',
            'division_operator_id' => $toUser->id,
        ]);

        return back()->with('success', 'Permohonan berhasil didisposisikan.');
    }

    public function uploadResult(Request $request, Submission $submission)
    {
        abort_unless($request->user()->role->value === 'analis_hukum', 403);

        $validated = $request->validate([
            'document_type' => ['required', Rule::in(['hasil_analisis', 'rekomendasi'])],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
            'mark_completed' => ['nullable', 'boolean'],
        ]);

        $resultFile = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Upload dokumen gagal. Periksa ukuran file dan coba lagi.'
        );

        $this->storeDocument($submission->id, $request->user()->id, $resultFile, $validated['document_type']);

        if ((bool) ($validated['mark_completed'] ?? false)) {
            $submission->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);
        }

        return back()->with('success', 'Dokumen hasil berhasil diunggah.');
    }

    private function storeDocument(int $submissionId, int $userId, UploadedFile $file, string $type): void
{
    $destinationPath = public_path('storage/permohonan');

    if (! is_dir($destinationPath) && ! mkdir($destinationPath, 0755, true) && ! is_dir($destinationPath)) {
        throw ValidationException::withMessages([
            'file' => 'Folder upload permohonan tidak dapat dibuat.',
        ]);
    }

    $originalName = $file->getClientOriginalName();
    $storedName = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName);

    $fileSize = $file->getSize();
    $mimeType = $file->getClientMimeType();

    // Baru pindahkan
    $file->move($destinationPath, $storedName);

    SubmissionDocument::query()->create([
        'submission_id' => $submissionId,
        'uploaded_by' => $userId,
        'document_type' => $type,
        'file_name' => $originalName,
        'file_path' => 'permohonan/'.$storedName,
        'mime_type' => $mimeType,
        'file_size' => $fileSize,
    ]);
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

    private function authorizeView(Request $request, Submission $submission): void
    {
        $role = $request->user()->role->value;

        if (in_array($role, ['operator_kanwil', 'kakanwil', 'kepala_divisi_p3h'], true)) {
            return;
        }

        if ($role === 'operator_pemda' && $submission->submitter_id === $request->user()->id) {
            return;
        }

        if ($role === 'analis_hukum' && $submission->assignments()->where('analyst_id', $request->user()->id)->exists()) {
            return;
        }

        abort(403);
    }
}
