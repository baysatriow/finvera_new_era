<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /* ============================================================
     * HALAMAN PROFIL USER
     * ============================================================ */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /* ============================================================
     * UPDATE DATA PROFIL
     * ============================================================ */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi dasar untuk semua role
        $rules = [
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string',
            'date_of_birth' => 'required|date',
            'province'      => 'required|string',
            'city'          => 'required|string',
            'district'      => 'required|string',
            'village'       => 'required|string',
            'postal_code'   => 'required|string',
            'address_full'  => 'required|string',
        ];

        // Validasi tambahan khusus borrower
        if ($user->role === 'borrower') {
            $rules['job']                 = 'required|string';
            $rules['monthly_income']      = 'required|numeric|min:0';
            $rules['employment_duration'] = 'required|integer|min:0';
        }

        $request->validate($rules);

        // Format nomor HP
        $phone = $this->formatPhoneNumber($request->phone);

        // Data umum yang diperbarui
        $data = [
            'name'          => $request->name,
            'phone'         => $phone,
            'date_of_birth' => $request->date_of_birth,
            'province'      => $request->province,
            'city'          => $request->city,
            'district'      => $request->district,
            'village'       => $request->village,
            'postal_code'   => $request->postal_code,
            'address_full'  => $request->address_full,
        ];

        // Tambahan data borrower
        if ($user->role === 'borrower') {
            $data['job']                 = $request->job;
            $data['monthly_income']      = $request->monthly_income;
            $data['employment_duration'] = $request->employment_duration;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /* ============================================================
     * UPDATE PASSWORD
     * ============================================================ */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Validasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /* ============================================================
     * HELPER — FORMAT NOMOR HP
     * ============================================================ */
    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);

        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1);
        }

        if (substr($number, 0, 1) === '8') {
            return '62' . $number;
        }

        return $number;
    }
}
