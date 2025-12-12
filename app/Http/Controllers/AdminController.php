<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\User;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /* ============================================================
     * DASHBOARD — STATISTIK UTAMA & CHART
     * ============================================================ */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'monthly');

        $stats = [
            'total_users' => User::where('role', 'borrower')->count(),
            'total_active_loans' => Loan::where('status', 'active')->count(),
            'total_disbursed' => Loan::whereIn('status', ['active', 'paid'])->sum('total_amount'),
            'pending_applications' => LoanApplication::where('status', 'pending')->count(),
        ];

        $chartData = $this->getChartData($filter);

        $recentApplications = LoanApplication::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'chartData', 'filter'));
    }

    /* ============================================================
     * APPLICATIONS — DAFTAR PENGAJUAN MASUK
     * ============================================================ */
    public function applications()
    {
        $applications = LoanApplication::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.applications.index', compact('applications'));
    }

    /* ============================================================
     * SHOW — DETAIL LENGKAP PENGAJUAN PINJAMAN
     * ============================================================ */
    public function showApplication($id)
    {
        $application = LoanApplication::with([
                'user.kyc',
                'user.bankAccounts'
            ])
            ->findOrFail($id);

        return view('admin.applications.show', compact('application'));
    }

    /* ============================================================
     * CHART DATA — MENGAMBIL DATA UNTUK GRAFIK
     * ============================================================ */
    private function getChartData($filter)
    {
        $labels = [];
        $values = [];

        $query = Loan::whereIn('status', ['active', 'paid']);

        if ($filter === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('D, d M');
                $values[] = (clone $query)->whereDate('created_at', $date)->sum('total_amount');
            }
        }

        elseif ($filter === 'yearly') {
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i)->format('Y');
                $labels[] = $year;
                $values[] = (clone $query)->whereYear('created_at', $year)->sum('total_amount');
            }
        }

        else {
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = Carbon::create(null, $i, 1)->format('M');
                $values[] = (clone $query)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', $i)
                    ->sum('total_amount');
            }
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /* ============================================================
     * APPROVE — SETUJUI PINJAMAN + NOTIFIKASI & CICILAN
     * ============================================================ */
    public function approve($id)
    {
        $application = LoanApplication::with('user')->findOrFail($id);

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();

        try {
            // Update status aplikasi
            $application->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'admin_note' => 'Disetujui secara manual oleh Admin.',
            ]);

            // Generate pinjaman aktif
            $loan = $this->generateActiveLoan($application);

            /* --- NOTIFIKASI: Pengajuan Disetujui --- */
            $application->user->notify(new SystemNotification([
                'title'   => 'Pengajuan Disetujui',
                'message' => 'Kabar gembira! Pengajuan pinjaman Anda sebesar Rp '
                              . number_format($application->amount, 0, ',', '.')
                              . ' telah disetujui. Dana akan segera dicairkan.',
                'type'    => 'success',
                'url'     => route('loans.show', $application->id),
            ]));

            /* --- NOTIFIKASI: Dana Cair --- */
            $application->user->notify(new SystemNotification([
                'title'   => 'Dana Telah Cair',
                'message' => 'Dana sebesar Rp '
                              . number_format($application->amount, 0, ',', '.')
                              . ' telah dicairkan ke rekening utama Anda.',
                'type'    => 'success',
                'url'     => route('installments.index'),
            ]));

            DB::commit();
            return redirect()->route('admin.applications')->with('success', 'Pinjaman berhasil disetujui.');

        } catch (\Exception $e) {

            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /* ============================================================
     * REJECT — TOLAK PENGAJUAN + NOTIFIKASI
     * ============================================================ */
    public function reject(Request $request, $id)
    {
        $application = LoanApplication::with('user')->findOrFail($id);

        if ($application->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Update status
        $application->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'admin_note' => $request->reason,
        ]);

        /* --- NOTIFIKASI: Ditolak --- */
        $application->user->notify(new SystemNotification([
            'title'   => 'Pengajuan Ditolak',
            'message' => 'Mohon maaf, pengajuan pinjaman Anda belum dapat kami setujui. Alasan: '
                          . $request->reason,
            'type'    => 'danger',
            'url'     => route('loans.show', $application->id),
        ]));

        return redirect()->route('admin.applications')->with('success', 'Pinjaman telah ditolak.');
    }

    /* ============================================================
     * HELPER — GENERATE PINJAMAN AKTIF + CICILAN
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
            'due_date'          => now()->addMonths((int) $application->tenor),
            'disbursed_at'      => now(),
        ]);

        $monthlyAmount = ceil($application->amount / (int) $application->tenor);

        for ($i = 1; $i <= (int) $application->tenor; $i++) {
            Installment::create([
                'loan_id'           => $loan->id,
                'installment_number'=> $i,
                'due_date'          => now()->addMonths($i),
                'amount'            => $monthlyAmount,
                'status'            => 'pending',
                'tazir_amount'      => 0,
                'tawidh_amount'     => 0,
            ]);
        }

        return $loan;
    }
}
