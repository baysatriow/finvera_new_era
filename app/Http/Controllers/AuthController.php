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
     * LOGIN
     * ============================================================ */

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input login (email / username)
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Tentukan apakah input adalah email atau username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Routing sesuai role
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            // Borrower belum isi alamat → redirect ke Step 2
            if ($user->role === 'borrower' && empty($user->address_full)) {
                return redirect()->route('register.step2');
            }

            return redirect()->intended('dashboard');
        }

        throw ValidationException::withMessages([
            'login' => 'Username/Email atau kata sandi salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda berhasil keluar.');
    }

    /* ============================================================
     * REGISTER — STEP 1 (Akun)
     * ============================================================ */

    public function showRegisterStep1()
    {
        return view('auth.register_step1');
    }

    public function storeRegisterStep1(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|min:4|max:16|unique:users|alpha_dash',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => ['required', 'string', 'regex:/^(\+62|62|08)[0-9]{8,15}$/'],
        ], [
            'username.unique' => 'Username ini sudah digunakan.',
            'username.min'    => 'Username terlalu pendek (minimal 4 karakter).',
            'email.unique'    => 'Email ini sudah terdaftar.',
            'phone.regex'     => 'Format nomor HP salah. Gunakan 08xx atau 62xx.',
        ]);

        $phone = $this->formatPhoneNumber($request->phone);

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
     * REGISTER — STEP 2 (Data Pribadi)
     * ============================================================ */

    public function showRegisterStep2()
    {
        return view('auth.register_step2');
    }

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
     * HELPER
     * ============================================================ */

    private function formatPhoneNumber($number)
    {
        // Hilangkan non-digit
        $number = preg_replace('/\D/', '', $number);

        // 08xxxx → 62xxxx
        if (substr($number, 0, 1) === '0') {
            return '62' . substr($number, 1);
        }

        // 8xxxx → 628xxxx
        if (substr($number, 0, 1) === '8') {
            return '62' . $number;
        }

        return $number;
    }
}
