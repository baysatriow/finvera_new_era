<?php

namespace App\Http\Controllers;

use App\Models\CompanyBankAccount;
use Illuminate\Http\Request;

class AdminBankController extends Controller
{
    /**
     * ============================================================
     * LANDING PAGE — DAFTAR REKENING PERUSAHAAN
     * ============================================================
     */
    public function index()
    {
        // Urutkan rekening aktif di paling atas
        $banks = CompanyBankAccount::orderBy('is_active', 'desc')->get();

        return view('admin.banks.index', compact('banks'));
    }

    /**
     * ============================================================
     * STORE — TAMBAH REKENING PERUSAHAAN
     * ============================================================
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name'       => 'required|string',
            'account_number'  => 'required|numeric',
            'account_holder'  => 'required|string',
        ]);


        $isActive = $request->has('is_active') || CompanyBankAccount::count() === 0;

        if ($isActive) {
            // Nonaktifkan semua rekening lain
            CompanyBankAccount::query()->update(['is_active' => false]);
        }

        CompanyBankAccount::create([
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'is_active'      => $isActive,
        ]);

        return back()->with('success', 'Rekening perusahaan berhasil ditambahkan.');
    }

    /**
     * ============================================================
     * UPDATE — EDIT REKENING PERUSAHAAN
     * ============================================================
     */
    public function update(Request $request, $id)
    {
        $bank = CompanyBankAccount::findOrFail($id);

        // Jika diubah menjadi aktif, nonaktifkan rekening lainnya
        if ($request->has('is_active') && $request->is_active) {
            CompanyBankAccount::where('id', '!=', $id)
                ->update(['is_active' => false]);
        }

        $bank->update([
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_holder' => $request->account_holder,
            'is_active'      => $request->has('is_active'),
        ]);

        return back()->with('success', 'Rekening perusahaan berhasil diperbarui.');
    }

    /**
     * ============================================================
     * SET PRIMARY — JADIKAN REKENING UTAMA
     * ============================================================
     */
    public function setPrimary($id)
    {
        // Nonaktifkan semua rekening
        CompanyBankAccount::query()->update(['is_active' => false]);

        // Aktifkan rekening terpilih
        $bank = CompanyBankAccount::findOrFail($id);
        $bank->update(['is_active' => true]);

        return back()->with('success', 'Rekening penerima utama berhasil diubah.');
    }

    /**
     * ============================================================
     * DELETE — HAPUS REKENING PERUSAHAAN
     * ============================================================
     */
    public function destroy($id)
    {
        $bank = CompanyBankAccount::findOrFail($id);

        // Jika rekening yang dihapus adalah aktif, aktifkan pengganti jika ada
        if ($bank->is_active) {
            $nextBank = CompanyBankAccount::where('id', '!=', $id)->first();

            if ($nextBank) {
                $nextBank->update(['is_active' => true]);
            }
        }

        $bank->delete();

        return back()->with('success', 'Rekening perusahaan berhasil dihapus.');
    }
}
