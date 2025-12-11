<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /* ============================================================
     * LIST ADMIN
     * ============================================================ */
    public function index()
    {
        // Hanya Admin Master yang boleh membuka halaman ini
        if (Auth::user()->admin_level !== 'master') {
            abort(403, 'Akses Ditolak. Hanya Admin Utama.');
        }

        $admins = User::where('role', 'admin')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', compact('admins'));
    }

    /* ============================================================
     * FORM TAMBAH ADMIN BARU
     * ============================================================ */
    public function create()
    {
        return view('admin.users.create');
    }

    /* ============================================================
     * SIMPAN ADMIN BARU
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users',
            'username'    => 'required|string|min:4|max:20|alpha_dash|unique:users',
            'password'    => 'required|string|min:8',
            'admin_level' => 'required|in:master,staff',
            'phone'       => 'required|numeric',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
            'role'        => 'admin',
            'admin_level' => $request->admin_level,
            'kyc_status'  => 'verified',  // Admin otomatis verified
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna admin berhasil ditambahkan.');
    }

    /* ============================================================
     * FORM EDIT ADMIN
     * ============================================================ */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /* ============================================================
     * UPDATE ADMIN
     * ============================================================ */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'admin_level' => 'required|in:master,staff',
            'phone'       => 'required|numeric',
            'password'    => 'nullable|string|min:8',
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'admin_level' => $request->admin_level,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    /* ============================================================
     * HAPUS ADMIN
     * ============================================================ */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
