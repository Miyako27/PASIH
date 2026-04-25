<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuideDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuideManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;
        $search = trim((string) $request->string('q'));

        $query = GuideDocument::query()
            ->with('uploader')
            ->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('document_title', 'like', "%{$search}%")
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') like ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') like ?", ["%{$search}%"])
                    ->orWhereHas('uploader', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return view('pages.admin.guides.index', [
            'guides' => $query->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('pages.admin.guides.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_title' => ['required', 'string', 'max:150'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
        ]);

        $file = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Upload buku panduan gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeGuideFile($file);

        GuideDocument::query()->create([
            'uploaded_by' => $request->user()->id,
            'file_name' => $stored['file_name'],
            'document_title' => trim((string) $validated['document_title']),
            'file_path' => $stored['file_path'],
            'mime_type' => $stored['mime_type'],
            'file_size' => $stored['file_size'],
        ]);

        return redirect()->route('admin.guides.index')->with('success', 'Buku panduan berhasil ditambahkan.');
    }

    public function show(GuideDocument $guide)
    {
        $guide->load('uploader');

        return view('pages.admin.guides.show', [
            'guide' => $guide,
        ]);
    }

    public function edit(GuideDocument $guide)
    {
        $guide->load('uploader');

        return view('pages.admin.guides.edit', [
            'guide' => $guide,
        ]);
    }

    public function update(Request $request, GuideDocument $guide)
    {
        $validated = $request->validate([
            'document_title' => ['required', 'string', 'max:150'],
            'file' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx'],
        ]);

        $documentTitle = trim((string) $validated['document_title']);

        if (! isset($validated['file'])) {
            $guide->update([
                'document_title' => $documentTitle,
            ]);

            return redirect()->route('admin.guides.show', $guide)->with('success', 'Tidak ada perubahan dokumen.');
        }

        $file = $this->validateUploadedFile(
            $request->file('file'),
            'file',
            'Update buku panduan gagal. Pastikan ukuran file tidak melebihi batas server.'
        );
        $stored = $this->storeGuideFile($file);

        $oldPath = trim((string) $guide->file_path);
        $guide->update([
            'uploaded_by' => $request->user()->id,
            'file_name' => $stored['file_name'],
            'document_title' => $documentTitle,
            'file_path' => $stored['file_path'],
            'mime_type' => $stored['mime_type'],
            'file_size' => $stored['file_size'],
        ]);

        $this->deletePhysicalFile($oldPath);

        return redirect()->route('admin.guides.show', $guide)->with('success', 'Buku panduan berhasil diperbarui.');
    }

    public function destroy(GuideDocument $guide)
    {
        $oldPath = trim((string) $guide->file_path);
        $guide->delete();
        $this->deletePhysicalFile($oldPath);

        return redirect()->route('admin.guides.index')->with('success', 'Buku panduan berhasil dihapus.');
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
    private function storeGuideFile(UploadedFile $file): array
    {
        $destinationPath = public_path('storage/buku-panduan');

        if (! is_dir($destinationPath) && ! mkdir($destinationPath, 0755, true) && ! is_dir($destinationPath)) {
            throw ValidationException::withMessages([
                'file' => 'Folder upload buku panduan tidak dapat dibuat.',
            ]);
        }

        $displayName = $this->buildDisplayDocumentName(now());
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
            'file_path' => 'buku-panduan/'.$storedName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ];
    }

    private function buildDisplayDocumentName(\Illuminate\Support\Carbon $timestamp): string
    {
        return 'Admin_BukuPanduan_'.$timestamp->format('YmdHis');
    }

    private function deletePhysicalFile(string $relativePath): void
    {
        if ($relativePath === '') {
            return;
        }

        $candidates = [
            public_path('storage/'.$relativePath),
            storage_path('app/public/'.$relativePath),
            public_path($relativePath),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                @unlink($path);
                return;
            }
        }
    }
}
