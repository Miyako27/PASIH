<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        $routeName = $request->route()?->getName();
        $method = strtoupper($request->method());

        if (! $user || ! $routeName) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        if (in_array($routeName, ['login.attempt', 'logout'], true)) {
            return $response;
        }

        if (in_array($method, ['HEAD', 'OPTIONS'], true)) {
            return $response;
        }

        $action = $this->resolveAction($routeName, $method);

        try {
            UserActivity::query()->create([
                'user_id' => $user->id,
                'type' => $action['type'],
                'title' => $action['title'],
                'detail' => $action['detail'],
                'route_name' => $routeName,
                'method' => $method,
            ]);
        } catch (\Throwable) {
            // Logging aktivitas tidak boleh mengganggu request utama.
        }

        return $response;
    }

    /**
     * @return array{type:string,title:string,detail:string}
     */
    private function resolveAction(string $routeName, string $method): array
    {
        return match ($routeName) {
            'dashboard' => ['type' => 'Navigasi', 'title' => 'Membuka dashboard', 'detail' => 'Mengakses halaman dashboard.'],
            'notifications.index' => ['type' => 'Notifikasi', 'title' => 'Membuka notifikasi', 'detail' => 'Melihat daftar notifikasi akun.'],

            'submissions.index' => ['type' => 'Permohonan', 'title' => 'Membuka daftar permohonan', 'detail' => 'Melihat data permohonan.'],
            'submissions.show' => ['type' => 'Permohonan', 'title' => 'Membuka detail permohonan', 'detail' => 'Melihat detail permohonan.'],
            'submissions.create' => ['type' => 'Permohonan', 'title' => 'Membuka form pengajuan', 'detail' => 'Mengakses form pembuatan pengajuan.'],
            'submissions.store' => ['type' => 'Permohonan', 'title' => 'Membuat pengajuan', 'detail' => 'Menyimpan data pengajuan baru.'],
            'submissions.edit' => ['type' => 'Permohonan', 'title' => 'Membuka form edit pengajuan', 'detail' => 'Mengakses form ubah data pengajuan.'],
            'submissions.update' => ['type' => 'Permohonan', 'title' => 'Memperbarui pengajuan', 'detail' => 'Menyimpan perubahan data pengajuan.'],
            'submissions.destroy' => ['type' => 'Permohonan', 'title' => 'Menghapus pengajuan', 'detail' => 'Menghapus data pengajuan.'],
            'submissions.status-disposisi.form' => ['type' => 'Permohonan', 'title' => 'Membuka form status dan disposisi', 'detail' => 'Mengakses form status/disposisi pengajuan.'],
            'submissions.status-disposisi.save' => ['type' => 'Permohonan', 'title' => 'Menyimpan status dan disposisi', 'detail' => 'Menyimpan status dan disposisi pengajuan.'],
            'submissions.update-status' => ['type' => 'Permohonan', 'title' => 'Memperbarui status pengajuan', 'detail' => 'Menyimpan perubahan status pengajuan.'],
            'submissions.dispose' => ['type' => 'Permohonan', 'title' => 'Menyimpan disposisi pengajuan', 'detail' => 'Menyimpan data disposisi pengajuan.'],
            'submissions.penugasan.form' => ['type' => 'Penugasan', 'title' => 'Membuka form penugasan', 'detail' => 'Mengakses form penugasan dari pengajuan.'],
            'submissions.penugasan.save' => ['type' => 'Penugasan', 'title' => 'Membuat penugasan dari pengajuan', 'detail' => 'Menyimpan data penugasan dari pengajuan.'],
            'submissions.upload-result' => ['type' => 'Permohonan', 'title' => 'Mengunggah dokumen hasil', 'detail' => 'Mengunggah dokumen hasil pada pengajuan.'],

            'assignments.index' => ['type' => 'Penugasan', 'title' => 'Membuka daftar penugasan', 'detail' => 'Melihat daftar penugasan.'],
            'assignments.show' => ['type' => 'Penugasan', 'title' => 'Membuka detail penugasan', 'detail' => 'Melihat detail penugasan.'],
            'assignments.store' => ['type' => 'Penugasan', 'title' => 'Membuat penugasan', 'detail' => 'Menyimpan data penugasan.'],
            'assignments.take' => ['type' => 'Penugasan', 'title' => 'Mengambil penugasan', 'detail' => 'Mengambil penugasan untuk diproses analis.'],
            'assignments.update-status' => ['type' => 'Penugasan', 'title' => 'Memperbarui status penugasan', 'detail' => 'Menyimpan perubahan status penugasan.'],
            'assignments.upload-hasil.form' => ['type' => 'Penugasan', 'title' => 'Membuka form upload hasil analisis', 'detail' => 'Mengakses form upload hasil analisis.'],
            'assignments.upload-hasil.store' => ['type' => 'Penugasan', 'title' => 'Mengunggah hasil analisis', 'detail' => 'Menyimpan dokumen hasil analisis.'],
            'assignments.upload-document' => ['type' => 'Penugasan', 'title' => 'Mengunggah dokumen penugasan', 'detail' => 'Menyimpan dokumen tambahan pada penugasan.'],

            'assignments.analysis-results' => ['type' => 'Hasil Analisis', 'title' => 'Membuka daftar hasil analisis', 'detail' => 'Melihat daftar hasil analisis.'],
            'assignments.analysis-results.show' => ['type' => 'Hasil Analisis', 'title' => 'Membuka detail hasil analisis', 'detail' => 'Melihat detail hasil analisis.'],
            'assignments.analysis-results.edit' => ['type' => 'Hasil Analisis', 'title' => 'Membuka form edit hasil analisis', 'detail' => 'Mengakses form edit hasil analisis.'],

            default => [
                'type' => 'Aktivitas Sistem',
                'title' => $this->fallbackTitle($method),
                'detail' => "Aksi pada route {$routeName}.",
            ],
        };
    }

    private function fallbackTitle(string $method): string
    {
        return match ($method) {
            'GET' => 'Membuka halaman sistem',
            'POST' => 'Menyimpan data sistem',
            'PUT', 'PATCH' => 'Memperbarui data sistem',
            'DELETE' => 'Menghapus data sistem',
            default => 'Menjalankan aksi sistem',
        };
    }
}
