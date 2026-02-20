<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;

class InstitutionManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 5);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 5;
        $search = trim((string) $request->string('q'));

        $query = Instansi::query()->latest('id_instansi');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('nama_instansi', 'like', "%{$search}%")
                    ->orWhere('jenis_instansi', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        return view('pages.admin.instansi.index', [
            'institutions' => $query->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('pages.admin.instansi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:150'],
            'jenis_instansi' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
        ]);

        Instansi::query()->create($validated);

        return redirect()->route('admin.instansi.index')->with('success', 'Data instansi berhasil ditambahkan.');
    }

    public function show(Instansi $instansi)
    {
        return view('pages.admin.instansi.show', [
            'institution' => $instansi,
        ]);
    }

    public function edit(Instansi $instansi)
    {
        return view('pages.admin.instansi.edit', [
            'institution' => $instansi,
        ]);
    }

    public function update(Request $request, Instansi $instansi)
    {
        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:150'],
            'jenis_instansi' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string'],
        ]);

        $instansi->update($validated);

        return redirect()->route('admin.instansi.index')->with('success', 'Data instansi berhasil diperbarui.');
    }

    public function destroy(Instansi $instansi)
    {
        $instansi->delete();

        return redirect()->route('admin.instansi.index')->with('success', 'Data instansi berhasil dihapus.');
    }
}
