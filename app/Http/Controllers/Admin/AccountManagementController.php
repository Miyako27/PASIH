<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [5, 10, 25], true) ? $perPage : 10;
        $search = trim((string) $request->string('q'));

        $query = User::query()
            ->with(['instansi'])
            ->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('instansi', function ($instansiQuery) use ($search): void {
                        $instansiQuery->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        return view('pages.admin.accounts.index', [
            'accounts' => $query->paginate($perPage)->withQueryString(),
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('pages.admin.accounts.create', [
            'roles' => Role::query()
                ->whereNotIn('nama_role', ['pimpinan_p3h', 'operator_divisi_p3h'])
                ->orderBy('nama_role')
                ->get(),
            'institutions' => Instansi::query()->orderBy('nama_instansi')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'nama_role')],
            'id_instansi' => ['required', 'exists:instansi,id_instansi'],
        ]);

        $roleId = Role::query()->where('nama_role', $validated['role'])->value('id_role');

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'id_role' => $roleId,
            'id_instansi' => (int) $validated['id_instansi'],
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Data akun berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load(['instansi']);

        return view('pages.admin.accounts.show', [
            'account' => $user,
        ]);
    }

    public function edit(User $user)
    {
        return view('pages.admin.accounts.edit', [
            'account' => $user,
            'roles' => Role::query()
                ->whereNotIn('nama_role', ['pimpinan_p3h', 'operator_divisi_p3h'])
                ->orderBy('nama_role')
                ->get(),
            'institutions' => Instansi::query()->orderBy('nama_instansi')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'nama_role')],
            'id_instansi' => ['required', 'exists:instansi,id_instansi'],
        ]);

        $roleId = Role::query()->where('nama_role', $validated['role'])->value('id_role');

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'id_role' => $roleId,
            'id_instansi' => (int) $validated['id_instansi'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.accounts.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors(['account' => 'Akun yang sedang login tidak dapat dihapus.']);
        }

        $user->delete();

        return redirect()->route('admin.accounts.index')->with('success', 'Data akun berhasil dihapus.');
    }
}
