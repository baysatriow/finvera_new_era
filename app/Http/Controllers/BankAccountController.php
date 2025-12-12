<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /* ============================================================
     * INDEX — LIST REKENING USER
     * ============================================================ */
    public function index()
    {
        $accounts = BankAccount::where('user_id', Auth::id())
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bank.index', compact('accounts'));
    }

    /* ============================================================
     * CREATE — FORM TAMBAH REKENING
     * ============================================================ */
    public function create()
    {
        return view('bank.create');
    }

    /* ============================================================
     * STORE — SIMPAN REKENING BARU
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name'           => 'required|string',
            'account_number'      => 'required|numeric',
            'account_holder_name' => 'required|string',
        ]);

        $user      = Auth::user();
        $inputName = strtolower($request->account_holder_name);
        $userName  = strtolower($user->name);

        // Validasi nama harus sesuai akun/KTP
        if (!str_contains($inputName, $userName) && !str_contains($userName, $inputName)) {
            return back()
                ->with('error', 'Nama pemilik rekening HARUS sesuai dengan nama pada akun/KTP Anda.')
                ->withInput();
        }

        // Jika rekening pertama → otomatis primary
        $isFirst   = !BankAccount::where('user_id', $user->id)->exists();
        $isPrimary = $request->has('is_primary') || $isFirst;

        if ($isPrimary) {
            BankAccount::where('user_id', $user->id)->update(['is_primary' => false]);
        }

        BankAccount::create([
            'user_id'             => $user->id,
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => strtoupper($request->account_holder_name),
            'is_primary'          => $isPrimary,
        ]);

        return redirect()
            ->route('bank.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    /* ============================================================
     * EDIT — FORM EDIT REKENING
     * ============================================================ */
    public function edit($id)
    {
        $account = BankAccount::where('user_id', Auth::id())->findOrFail($id);
        return view('bank.edit', compact('account'));
    }

    /* ============================================================
     * UPDATE — PERBARUI DATA REKENING
     * ============================================================ */
    public function update(Request $request, $id)
    {
        $account = BankAccount::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'bank_name'           => 'required|string',
            'account_number'      => 'required|numeric',
            'account_holder_name' => 'required|string',
        ]);

        $user      = Auth::user();
        $inputName = strtolower($request->account_holder_name);
        $userName  = strtolower($user->name);

        // Validasi nama harus sesuai akun/KTP
        if (!str_contains($inputName, $userName) && !str_contains($userName, $inputName)) {
            return back()
                ->with('error', 'Nama pemilik rekening HARUS sesuai dengan nama pada akun/KTP Anda.')
                ->withInput();
        }

        $account->update([
            'bank_name'           => $request->bank_name,
            'account_number'      => $request->account_number,
            'account_holder_name' => strtoupper($request->account_holder_name),
        ]);

        return redirect()
            ->route('bank.index')
            ->with('success', 'Data rekening berhasil diperbarui.');
    }

    /* ============================================================
     * SET PRIMARY — JADIKAN REKENING UTAMA
     * ============================================================ */
    public function setPrimary($id)
    {
        $user    = Auth::user();
        $account = BankAccount::where('user_id', $user->id)->findOrFail($id);

        BankAccount::where('user_id', $user->id)->update(['is_primary' => false]);
        $account->update(['is_primary' => true]);

        return back()->with('success', 'Rekening utama berhasil diubah.');
    }

    /* ============================================================
     * DESTROY — HAPUS REKENING
     * ============================================================ */
    public function destroy($id)
    {
        $account = BankAccount::where('user_id', Auth::id())->findOrFail($id);

        // Jika menghapus rekening utama → set rekening lain sebagai primary
        if ($account->is_primary) {
            $nextAccount = BankAccount::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->first();

            if ($nextAccount) {
                $nextAccount->update(['is_primary' => true]);
            }
        }

        $account->delete();

        return back()->with('success', 'Rekening berhasil dihapus.');
    }
}
