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
            'dashboard' => ['type' => 'Navigasi', 'title' => 'Membuka dashboard', 'detail' => 'Mengakses halaman dashboard'],
            'notifications.index' => ['type' => 'Notifikasi', 'title' => 'Membuka notifikasi', 'detail' => 'Melihat daftar notifikasi akun'],
            'password.change' => ['type' => 'Akun', 'title' => 'Membuka ubah password', 'detail' => 'Mengakses halaman ubah password akun'],
            'guides.index' => ['type' => 'Buku Panduan', 'title' => 'Membuka daftar buku panduan', 'detail' => 'Melihat daftar buku panduan sistem'],

            'documents.preview.submission' => ['type' => 'Dokumen', 'title' => 'Membuka dokumen permohonan', 'detail' => 'Melihat pratinjau dokumen permohonan'],
            'documents.preview.assignment' => ['type' => 'Dokumen', 'title' => 'Membuka dokumen penugasan', 'detail' => 'Melihat pratinjau dokumen penugasan'],
            'documents.preview.suratbalasan' => ['type' => 'Dokumen', 'title' => 'Membuka dokumen surat balasan', 'detail' => 'Melihat pratinjau dokumen surat balasan'],
            'documents.preview.guide' => ['type' => 'Dokumen', 'title' => 'Membuka dokumen buku panduan', 'detail' => 'Melihat pratinjau dokumen buku panduan'],

            'submissions.index' => ['type' => 'Permohonan', 'title' => 'Membuka daftar permohonan', 'detail' => 'Melihat data permohonan'],
            'submissions.show' => ['type' => 'Permohonan', 'title' => 'Membuka detail permohonan', 'detail' => 'Melihat detail permohonan'],
            'submissions.create' => ['type' => 'Permohonan', 'title' => 'Membuka form pengajuan', 'detail' => 'Mengakses form pembuatan pengajuan'],
            'submissions.store' => ['type' => 'Permohonan', 'title' => 'Membuat pengajuan', 'detail' => 'Menyimpan data pengajuan baru'],
            'submissions.edit' => ['type' => 'Permohonan', 'title' => 'Membuka form edit pengajuan', 'detail' => 'Mengakses form ubah data pengajuan'],
            'submissions.update' => ['type' => 'Permohonan', 'title' => 'Memperbarui pengajuan', 'detail' => 'Menyimpan perubahan data pengajuan'],
            'submissions.destroy' => ['type' => 'Permohonan', 'title' => 'Menghapus pengajuan', 'detail' => 'Menghapus data pengajuan'],
            'submissions.status-disposisi.form' => ['type' => 'Permohonan', 'title' => 'Membuka form status dan disposisi', 'detail' => 'Mengakses form status/disposisi pengajuan'],
            'submissions.status-disposisi.save' => ['type' => 'Permohonan', 'title' => 'Menyimpan status dan disposisi', 'detail' => 'Menyimpan status dan disposisi pengajuan'],
            'submissions.penugasan.form' => ['type' => 'Penugasan', 'title' => 'Membuka form penugasan', 'detail' => 'Mengakses form penugasan dari pengajuan'],
            'submissions.penugasan.save' => ['type' => 'Penugasan', 'title' => 'Membuat penugasan dari pengajuan', 'detail' => 'Menyimpan data penugasan dari pengajuan'],

            'assignments.index' => ['type' => 'Penugasan', 'title' => 'Membuka daftar penugasan', 'detail' => 'Melihat daftar penugasan'],
            'assignments.show' => ['type' => 'Penugasan', 'title' => 'Membuka detail penugasan', 'detail' => 'Melihat detail penugasan'],
            'assignments.assign-pic.form' => ['type' => 'Penugasan', 'title' => 'Membuka form penentuan Penanggung Jawab', 'detail' => 'Mengakses form untuk menentukan Penanggung Jawab analis'],
            'assignments.assign-pic.store' => ['type' => 'Penugasan', 'title' => 'Menentukan Penanggung Jawab penugasan', 'detail' => 'Menyimpan Penanggung Jawab analis untuk penugasan'],
            'assignments.approval.form' => ['type' => 'Penugasan', 'title' => 'Membuka form Persetujuan penugasan', 'detail' => 'Mengakses form Persetujuan atau revisi hasil analisis'],
            'assignments.approval.store' => ['type' => 'Penugasan', 'title' => 'Menyimpan keputusan Persetujuan penugasan', 'detail' => 'Menyimpan keputusan Persetujuan atau revisi penugasan'],
            'assignments.upload-hasil.form' => ['type' => 'Penugasan', 'title' => 'Membuka form upload hasil analisis', 'detail' => 'Mengakses form upload hasil analisis'],
            'assignments.upload-hasil.store' => ['type' => 'Penugasan', 'title' => 'Mengunggah hasil analisis', 'detail' => 'Menyimpan dokumen hasil analisis'],

            'assignments.analysis-results' => ['type' => 'Hasil Analisis', 'title' => 'Membuka daftar hasil analisis', 'detail' => 'Melihat daftar hasil analisis'],
            'assignments.analysis-results.show' => ['type' => 'Hasil Analisis', 'title' => 'Membuka detail hasil analisis', 'detail' => 'Melihat detail hasil analisis'],

            'admin.accounts.index' => ['type' => 'Manajemen Akun', 'title' => 'Membuka daftar akun', 'detail' => 'Melihat daftar akun pengguna'],
            'admin.accounts.show' => ['type' => 'Manajemen Akun', 'title' => 'Membuka detail akun', 'detail' => 'Melihat detail akun pengguna'],
            'admin.accounts.create' => ['type' => 'Manajemen Akun', 'title' => 'Membuka form tambah akun', 'detail' => 'Mengakses form pembuatan akun pengguna'],
            'admin.accounts.store' => ['type' => 'Manajemen Akun', 'title' => 'Menambahkan akun', 'detail' => 'Menyimpan data akun pengguna baru'],
            'admin.accounts.edit' => ['type' => 'Manajemen Akun', 'title' => 'Membuka form edit akun', 'detail' => 'Mengakses form ubah data akun pengguna'],
            'admin.accounts.update' => ['type' => 'Manajemen Akun', 'title' => 'Memperbarui akun', 'detail' => 'Menyimpan perubahan data akun pengguna'],
            'admin.accounts.destroy' => ['type' => 'Manajemen Akun', 'title' => 'Menghapus akun', 'detail' => 'Menghapus data akun pengguna'],

            'admin.instansi.index' => ['type' => 'Manajemen Instansi', 'title' => 'Membuka daftar instansi', 'detail' => 'Melihat daftar instansi'],
            'admin.instansi.show' => ['type' => 'Manajemen Instansi', 'title' => 'Membuka detail instansi', 'detail' => 'Melihat detail data instansi'],
            'admin.instansi.create' => ['type' => 'Manajemen Instansi', 'title' => 'Membuka form tambah instansi', 'detail' => 'Mengakses form pembuatan data instansi'],
            'admin.instansi.store' => ['type' => 'Manajemen Instansi', 'title' => 'Menambahkan instansi', 'detail' => 'Menyimpan data instansi baru'],
            'admin.instansi.edit' => ['type' => 'Manajemen Instansi', 'title' => 'Membuka form edit instansi', 'detail' => 'Mengakses form ubah data instansi'],
            'admin.instansi.update' => ['type' => 'Manajemen Instansi', 'title' => 'Memperbarui instansi', 'detail' => 'Menyimpan perubahan data instansi'],
            'admin.instansi.destroy' => ['type' => 'Manajemen Instansi', 'title' => 'Menghapus instansi', 'detail' => 'Menghapus data instansi'],

            'admin.guides.index' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Membuka daftar buku panduan', 'detail' => 'Melihat daftar buku panduan'],
            'admin.guides.show' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Membuka detail buku panduan', 'detail' => 'Melihat detail buku panduan'],
            'admin.guides.create' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Membuka form tambah buku panduan', 'detail' => 'Mengakses form pembuatan buku panduan'],
            'admin.guides.store' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Menambahkan buku panduan', 'detail' => 'Menyimpan data buku panduan baru'],
            'admin.guides.edit' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Membuka form edit buku panduan', 'detail' => 'Mengakses form ubah data buku panduan'],
            'admin.guides.update' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Memperbarui buku panduan', 'detail' => 'Menyimpan perubahan data buku panduan'],
            'admin.guides.destroy' => ['type' => 'Manajemen Buku Panduan', 'title' => 'Menghapus buku panduan', 'detail' => 'Menghapus data buku panduan'],

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
