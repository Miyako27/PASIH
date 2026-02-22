<?php

namespace App\Http\Controllers;

use App\Models\AssignmentDocument;
use App\Models\SubmissionDocument;
use Illuminate\Http\Request;

class DocumentPreviewController extends Controller
{
    public function previewSubmission(Request $request, SubmissionDocument $document)
    {
        return $this->previewPdf($request, $document->file_path, $document->file_name, $document->mime_type);
    }

    public function previewAssignment(Request $request, AssignmentDocument $document)
    {
        return $this->previewPdf($request, $document->file_path, $document->file_name, $document->mime_type);
    }

    private function previewPdf(Request $request, ?string $relativePath, ?string $fileName, ?string $mimeType)
    {
        if (empty($relativePath)) {
            abort(404, 'File tidak tersedia.');
        }

        $detectedMime = strtolower((string) $mimeType);
        $detectedName = strtolower((string) $fileName);
        $detectedPath = strtolower((string) $relativePath);
        $isPdf = str_contains($detectedMime, 'pdf')
            || str_ends_with($detectedName, '.pdf')
            || str_ends_with($detectedPath, '.pdf');

        if (! $isPdf) {
            abort(404, 'Dokumen bukan PDF.');
        }

        $candidatePaths = [
            storage_path('app/public/'.$relativePath),
            public_path('storage/'.$relativePath),
            public_path($relativePath),
        ];

        $absolutePath = collect($candidatePaths)->first(static fn ($path) => is_file($path));

        if (! $absolutePath) {
            abort(404, 'File tidak ditemukan.');
        }

        $safeName = basename($fileName ?: 'document.pdf');
        $fileBytes = file_get_contents($absolutePath);

        if ($request->boolean('base64')) {
            return response()->json([
                'name' => $safeName,
                'mime_type' => 'application/pdf',
                'size' => filesize($absolutePath),
                'data' => base64_encode($fileBytes ?: ''),
            ]);
        }

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$safeName.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
