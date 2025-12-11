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
     * HALAMAN LAPORAN — RINGKASAN & GRAFIK
     * ============================================================ */
    public function index(Request $request)
    {
        // Filter tanggal (default: 30 hari terakhir)
        $startDate = $request->input('start_date', Carbon::now()->subDays(29)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        /* ------------------------------------------------------------
         * STATISTIK UTAMA
         * ---------------------------------------------------------- */

        // Total uang cair (disbursement) dalam periode
        $totalDisbursed = Loan::whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->sum('total_amount');

        // Total repayment masuk (uang kembali)
        $totalRepayment = Installment::where('status', 'paid')
            ->whereBetween('paid_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->sum(DB::raw('amount + tazir_amount + tawidh_amount'));

        // Estimasi pendapatan admin (admin_fee dari produk default)
        $productFee = LoanProduct::first()->admin_fee ?? 50000;

        $loanCount = Loan::whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->count();

        $totalRevenue = $loanCount * $productFee;

        /* ------------------------------------------------------------
         * DATA GRAFIK HARIAN
         * ---------------------------------------------------------- */
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
     * EXPORT CSV LAPORAN
     * ============================================================ */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $filename = "Laporan_FinVera_{$startDate}_sd_{$endDate}.csv";

        // Header CSV
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        // Data pencairan (outflow)
        $loans = Loan::with('user')
            ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
            ->whereBetween('disbursed_at', [
                $startDate . ' 00:00:00',
                $endDate   . ' 23:59:59'
            ])
            ->get();

        $callback = function () use ($loans) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'ID Pinjaman', 'Tanggal Cair', 'Nama Peminjam',
                'Tipe Transaksi', 'Nominal (Rp)', 'Status'
            ]);

            // Data pencairan
            foreach ($loans as $loan) {
                fputcsv($file, [
                    $loan->loan_code,
                    $loan->disbursed_at->format('Y-m-d H:i'),
                    $loan->user->name,
                    'PENCAIRAN (OUT)',
                    $loan->total_amount,
                    strtoupper($loan->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ============================================================
     * GENERATE DATA GRAFIK PER HARI
     * ============================================================ */
    private function getChartData($startDate, $endDate)
    {
        $start     = Carbon::parse($startDate);
        $end       = Carbon::parse($endDate);

        $labels            = [];
        $disbursementData  = [];
        $repaymentData     = [];

        // Loop semua hari dalam range
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dayStr = $date->format('Y-m-d');

            $labels[] = $date->format('d M');

            // Total disbursement pada hari tersebut
            $disbursementData[] = Loan::whereDate('disbursed_at', $dayStr)
                ->whereIn('status', ['active', 'paid', 'past_due', 'default'])
                ->sum('total_amount');

            // Total repayment masuk pada hari tersebut
            $repaymentData[] = Installment::whereDate('paid_at', $dayStr)
                ->where('status', 'paid')
                ->sum('total_paid'); // total_paid = amount + denda
        }

        return [
            'labels'       => $labels,
            'disbursement' => $disbursementData,
            'repayment'    => $repaymentData,
        ];
    }
}
