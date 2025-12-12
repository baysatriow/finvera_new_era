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
     * INDEX — LIST ADMIN (KHUSUS MASTER ADMIN)
     * ============================================================ */
    public function index()
    {
        if (Auth::user()->admin_level !== 'master') {
            abort(403, 'Akses Ditolak. Hanya Admin Utama.');
        }

        $admins = User::where('role', 'admin')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.index', compact('admins'));
    }

    /* ============================================================
     * CREATE — FORM TAMBAH ADMIN
     * ============================================================ */
    public function create()
    {
        return view('admin.users.create');
    }

    /* ============================================================
     * STORE — SIMPAN ADMIN BARU
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'admin_level' => 'required|in:master,staff',
            'username'    => 'required|string|min:4|max:20|alpha_dash|unique:users,username',
            'email'       => 'required|email|max:255|unique:users,email',
            'phone'       => 'required|numeric|unique:users,phone',
            'password'    => 'required|string|min:8',
        ], [
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone,
            'role'        => 'admin',
            'admin_level' => $request->admin_level,
            'kyc_status'  => 'verified',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna admin berhasil ditambahkan.');
    }

    /* ============================================================
     * EDIT — FORM EDIT ADMIN
     * ============================================================ */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /* ============================================================
     * UPDATE — UPDATE DATA ADMIN (IGNORE ID SENDIRI)
     * ============================================================ */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'admin_level' => 'required|in:master,staff',
            'email'       => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'required',
                'numeric',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8',
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'username'    => $request->username,
            'phone'       => $request->phone,
            'admin_level' => $request->admin_level,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    /* ============================================================
     * DESTROY — HAPUS ADMIN
     * ============================================================ */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
