<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /* ============================================================
     * DASHBOARD ADMIN
     * ============================================================ */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'monthly'); // weekly | monthly | yearly

        // Ringkasan Statistik
        $stats = [
            'total_users'          => User::where('role', 'borrower')->count(),
            'total_active_loans'   => Loan::where('status', 'active')->count(),
            'total_disbursed'      => Loan::whereIn('status', ['active', 'paid'])->sum('total_amount'),
            'pending_applications' => LoanApplication::where('status', 'pending')->count(),
        ];

        // Data Grafik
        $chartData = $this->getChartData($filter);

        // Daftar Pengajuan Terbaru
        $recentApplications = LoanApplication::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentApplications',
            'chartData',
            'filter'
        ));
    }

    /* ============================================================
     * GENERATE DATA GRAFIK DASHBOARD
     * ============================================================ */
    private function getChartData($filter)
    {
        $labels = [];
        $values = [];

        $baseQuery = Loan::query()
            ->whereIn('status', ['active', 'paid']);

        /* ----------------------
         * FILTER: 7 HARI TERAKHIR
         * ---------------------- */
        if ($filter === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D, d M');

                $amount = (clone $baseQuery)
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');

                $values[] = $amount;
            }

        /* ----------------------
         * FILTER: 5 TAHUN TERAKHIR
         * ---------------------- */
        } elseif ($filter === 'yearly') {
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i)->format('Y');
                $labels[] = $year;

                $amount = (clone $baseQuery)
                    ->whereYear('created_at', $year)
                    ->sum('total_amount');

                $values[] = $amount;
            }

        /* ----------------------
         * FILTER DEFAULT: BULAN DALAM SATU TAHUN
         * ---------------------- */
        } else {
            $yearNow = Carbon::now()->year;

            for ($month = 1; $month <= 12; $month++) {
                $labels[] = Carbon::create(null, $month, 1)->format('M');

                $amount = (clone $baseQuery)
                    ->whereYear('created_at', $yearNow)
                    ->whereMonth('created_at', $month)
                    ->sum('total_amount');

                $values[] = $amount;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /* ============================================================
     * LIST PENGAJUAN PINJAMAN
     * ============================================================ */
    public function applications()
    {
        $applications = LoanApplication::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.applications', compact('applications'));
    }

    /* ============================================================
     * APPROVE PENGAJUAN PINJAMAN
     * ============================================================ */
    public function approve($id)
    {
        $application = LoanApplication::findOrFail($id);

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan sudah diproses.');
        }

        DB::beginTransaction();
        try {
            $application->update([
                'status'      => 'approved',
                'reviewed_at' => now(),
                'admin_note'  => 'Disetujui Admin',
            ]);

            $this->generateActiveLoan($application);

            DB::commit();
            return back()->with('success', 'Pinjaman berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /* ============================================================
     * REJECT PENGAJUAN PINJAMAN
     * ============================================================ */
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required']);

        $application = LoanApplication::findOrFail($id);

        $application->update([
            'status'      => 'rejected',
            'reviewed_at' => now(),
            'admin_note'  => $request->reason,
        ]);

        return back()->with('success', 'Pinjaman berhasil ditolak.');
    }

    /* ============================================================
     * GENERATE LOAN AKTIF + JADWAL CICILAN
     * ============================================================ */
    private function generateActiveLoan(LoanApplication $application)
    {
        $loan = Loan::create([
            'user_id'           => $application->user_id,
            'application_id'    => $application->id,
            'loan_code'         => 'LN-' . strtoupper(Str::random(8)),
            'total_amount'      => $application->amount,
            'remaining_balance' => $application->amount,
            'status'            => 'active',
            'start_date'        => now(),
            'due_date'          => now()->addMonths($application->tenor),
            'disbursed_at'      => now(),
        ]);

        $monthlyAmount = ceil($application->amount / $application->tenor);

        for ($i = 1; $i <= $application->tenor; $i++) {
            Installment::create([
                'loan_id'           => $loan->id,
                'installment_number'=> $i,
                'due_date'          => now()->addMonths($i),
                'amount'            => $monthlyAmount,
                'status'            => 'pending',
            ]);
        }
    }
}
