<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    /* ============================================================
     * INDEX — HALAMAN UTAMA LAPORAN & ANALITIK
     * ============================================================ */
    public function index(Request $request)
    {
        /* ------------------------------
         * 1. FILTER RANGE TANGGAL
         * ------------------------------ */
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        /* ------------------------------
         * 2. HITUNG STATISTIK DASAR
         * ------------------------------ */
        $totalDisbursed = Loan::whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->sum('total_amount');

        $totalRepayment = Installment::where('status', 'paid')
            ->whereBetween('paid_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->sum(DB::raw('amount + tazir_amount + tawidh_amount'));

        $productFee  = LoanProduct::first()->admin_fee ?? 50000;
        $loanCount   = Loan::whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->count();

        $totalRevenue = $loanCount * $productFee;

        /* ------------------------------
         * 3. GRAFIK (HARIAN)
         * ------------------------------ */
        $chartData = $this->getChartData($startDate, $endDate);

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalDisbursed',
            'totalRepayment',
            'totalRevenue',
            'chartData'
        ));
    }

    /* ============================================================
     * EXPORT — EXPORT CSV LAPORAN TRANSAKSI
     * ============================================================ */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $filename = "Laporan_FinVera_{$startDate}_sd_{$endDate}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $loans = Loan::with('user')
            ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->get();

        $callback = function() use ($loans) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID Pinjaman',
                'Tanggal Cair',
                'Nama Peminjam',
                'Tipe Transaksi',
                'Nominal (Rp)',
                'Status'
            ]);

            // Isi Data
            foreach ($loans as $loan) {
                fputcsv($file, [
                    $loan->loan_code,
                    $loan->disbursed_at->format('Y-m-d H:i'),
                    $loan->user->name,
                    'PENCAIRAN',
                    $loan->total_amount,
                    strtoupper($loan->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ============================================================
     * HELPER — GENERATE DATA GRAFIK (PER HARI)
     * ============================================================ */
    private function getChartData($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        $labels           = [];
        $disbursementData = [];
        $repaymentData    = [];

        /* ------------------------------
         * LOOP HARIAN SESUAI RANGE
         * ------------------------------ */
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {

            $day = $date->format('Y-m-d');
            $labels[] = $date->format('d M'); // Contoh: "01 Jan"

            // Total pencairan
            $disbursementData[] = Loan::whereIn('status', ['active', 'paid', 'past_due', 'default'])
                ->whereDate('disbursed_at', $day)
                ->sum('total_amount');

            // Total pelunasan (cicilan)
            $repaymentData[] = Installment::where('status', 'paid')
                ->whereDate('paid_at', $day)
                ->sum('total_paid');
        }

        return [
            'labels'        => $labels,
            'disbursement'  => $disbursementData,
            'repayment'     => $repaymentData
        ];
    }
}
