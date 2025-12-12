<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /* ============================================================
     * LOGIN — FORM LOGIN
     * ============================================================ */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /* ============================================================
     * LOGIN — PROSES LOGIN (EMAIL / USERNAME)
     * ============================================================ */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.'
        ]);

        // Tentukan apakah user login menggunakan email atau username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect admin
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Borrower wajib lengkap data pribadi jika belum
            if ($user->role === 'borrower' && empty($user->address_full)) {
                return redirect()->route('register.step2');
            }

            return redirect()->intended('dashboard');
        }

        throw ValidationException::withMessages([
            'login' => 'Username/Email atau kata sandi salah.',
        ]);
    }

    /* ============================================================
     * LOGOUT — KELUAR DARI SISTEM
     * ============================================================ */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda berhasil keluar.');
    }

    /* ============================================================
     * REGISTER STEP 1 — FORM REGISTRASI AKUN
     * ============================================================ */
    public function showRegisterStep1()
    {
        return view('auth.register_step1');
    }

    /* ============================================================
     * REGISTER STEP 1 — SIMPAN AKUN (USERNAME, EMAIL, PASSWORD)
     * ============================================================ */
    public function storeRegisterStep1(Request $request)
    {
        // Normalisasi nomor HP agar valid untuk pengecekan UNIQUE
        $rawPhone = $request->phone;
        $phone    = $this->formatPhoneNumber($rawPhone);

        // Inject nomor telepon yang sudah diformat
        $request->merge(['phone' => $phone]);

        // Validasi data akun
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|min:4|max:16|unique:users|alpha_dash',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => ['required', 'string', 'numeric', 'unique:users,phone'],
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'username.required'    => 'Username wajib diisi.',
            'username.min'         => 'Username minimal 4 karakter.',
            'username.max'         => 'Username maksimal 16 karakter.',
            'username.unique'      => 'Username sudah digunakan.',
            'username.alpha_dash'  => 'Username hanya boleh berisi huruf, angka, strip, dan underscore.',
            'email.unique'         => 'Email sudah terdaftar.',
            'password.min'         => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi kata sandi tidak cocok.',
            'phone.numeric'        => 'Nomor HP harus berupa angka.',
            'phone.unique'         => 'Nomor HP sudah terdaftar.',
        ]);

        // Simpan user borrower
        $user = User::create([
            'name'        => $request->name,
            'username'    => $request->username,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $phone,
            'role'        => 'borrower',
            'kyc_status'  => 'unverified',
        ]);

        Auth::login($user);

        return redirect()
            ->route('register.step2')
            ->with('success', 'Akun berhasil dibuat! Silakan lengkapi data diri Anda.');
    }

    /* ============================================================
     * REGISTER STEP 2 — FORM DATA PRIBADI
     * ============================================================ */
    public function showRegisterStep2()
    {
        return view('auth.register_step2');
    }

    /* ============================================================
     * REGISTER STEP 2 — SIMPAN DATA PRIBADI
     * ============================================================ */
    public function storeRegisterStep2(Request $request)
    {
        $request->validate([
            'date_of_birth'       => 'required|date',
            'job'                 => 'required|string',
            'monthly_income'      => 'required|numeric|min:0',
            'employment_duration' => 'required|integer|min:0',
            'province'            => 'required|string',
            'city'                => 'required|string',
            'district'            => 'required|string',
            'village'             => 'required|string',
            'postal_code'         => 'required|string',
            'address_full'        => 'required|string',
            'tos_agreement'       => 'accepted',
        ]);

        $user = Auth::user();

        $user->update([
            'date_of_birth'       => $request->date_of_birth,
            'job'                 => $request->job,
            'monthly_income'      => $request->monthly_income,
            'employment_duration' => $request->employment_duration,
            'province'            => $request->province,
            'city'                => $request->city,
            'district'            => $request->district,
            'village'             => $request->village,
            'postal_code'         => $request->postal_code,
            'address_full'        => $request->address_full,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi selesai! Selamat datang di FinVera.');
    }

    /* ============================================================
     * HELPER — FORMAT NOMOR HP KE FORMAT INTERNASIONAL (62)
     * ============================================================ */
    private function formatPhoneNumber($number)
    {
        // Buang semua karakter selain angka
        $number = preg_replace('/\D/', '', $number);

        // Jika mulai dengan 0 → ubah ke 62
        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1);
        }

        // Jika mulai dengan 8 → tambahkan 62
        if (substr($number, 0, 1) === '8') {
            return '62' . $number;
        }

        // Jika sudah 62 → biarkan
        return $number;
    }
}
