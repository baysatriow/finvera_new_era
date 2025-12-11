<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /* ============================================================
     * LIST REKENING
     * ============================================================ */
    public function index()
    {
        $accounts = BankAccount::where('user_id', Auth::id())->get();
        return view('bank.index', compact('accounts'));
    }

    /* ============================================================
     * FORM TAMBAH REKENING
     * ============================================================ */
    public function create()
    {
        return view('bank.create');
    }

    /* ============================================================
     * SIMPAN REKENING BARU
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name'          => 'required|string',
            'account_number'     => 'required|numeric',
            'account_holder_name'=> 'required|string',
        ]);

        $user = Auth::user();

        /* ------------------------------------------------------------
         * VALIDASI NAMA PEMILIK REKENING
         * Nama rekening harus mengandung nama user (case-insensitive)
         * ------------------------------------------------------------ */
        $inputName = strtolower($request->account_holder_name);
        $userName  = strtolower($user->name);

        if (!str_contains($inputName, $userName) && !str_contains($userName, $inputName)) {
            return back()
                ->with('error', 'Nama pemilik rekening HARUS sesuai dengan nama pada akun/KTP Anda.')
                ->withInput();
        }

        /* ------------------------------------------------------------
         * SET PRIMARY ACCOUNT
         * Hapus primary dari rekening sebelumnya
         * ------------------------------------------------------------ */
        BankAccount::where('user_id', $user->id)->update(['is_primary' => false]);

        BankAccount::create([
            'user_id'             => $user->id,
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => strtoupper($request->account_holder_name),
            'is_primary'          => true,
        ]);

        return redirect()
            ->route('bank.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    /* ============================================================
     * HAPUS REKENING
     * ============================================================ */
    public function destroy($id)
    {
        $account = BankAccount::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $account->delete();

        return back()->with('success', 'Rekening berhasil dihapus.');
    }
}
