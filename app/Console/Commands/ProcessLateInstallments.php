<?php

namespace App\Console\Commands;

use App\Models\Installment;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessLateInstallments extends Command
{
    protected $signature = 'app:process-late-installments';

    protected $description = 'Processing late installments (Grace Period & Ta\'zir) and sending reminders';

    public function handle()
    {
        $this->info('Start processing late installments...');

        $installments = Installment::with(['loan.user'])
            ->where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now())
            ->get();

        foreach ($installments as $installment) {
            try {
                DB::transaction(function () use ($installment) {
                    $dueDate = Carbon::parse($installment->due_date);
                    $daysLate = (int) $dueDate->diffInDays(now(), false);

                    if ($daysLate <= 0) {
                        return;
                    }

                    if ($installment->status !== 'late') {
                        $installment->update(['status' => 'late']);
                    }

                    if ($daysLate >= 4 && $installment->tazir_amount == 0) {
                        $installment->update([
                            'tazir_amount' => 25000
                        ]);

                        $this->info("Applied Ta'zir Rp 25,000 to Installment ID: {$installment->id}");
                    }

                    if ($installment->last_reminder_day === $daysLate) {
                        return;
                    }

                    $user = $installment->loan->user;
                    $message = null;
                    $title = 'Tagihan Jatuh Tempo';
                    $type = 'warning';

                    if ($daysLate === 1) {
                        $message = 'Tagihan Anda lewat 1 hari. Segera bayar untuk menghindari denda.';
                    } elseif ($daysLate === 3) {
                        $message = 'HARI TERAKHIR BEBAS DENDA! Besok akan dikenakan Ta\'zir sosial Rp 25.000.';
                        $type = 'danger';
                    } elseif ($daysLate === 4) {
                        $title = 'Denda Ta\'zir Diterapkan';
                        $message = 'Tagihan telat 4 hari. Anda dikenakan Ta\'zir Rp 25.000 (Donasi Sosial).';
                        $type = 'danger';
                    } elseif ($daysLate === 7) {
                        $message = 'Tagihan telat 1 minggu. Mohon segera selesaikan kewajiban Anda.';
                        $type = 'danger';
                    } elseif ($daysLate === 14) {
                        $title = 'Peringatan Terakhir';
                        $message = 'Keterlambatan 14 hari. Mohon bertaqwa dan penuhi akad pinjaman Anda segera.';
                        $type = 'danger';
                    }

                    if ($message) {
                        $user->notify(new SystemNotification([
                            'title'   => $title,
                            'message' => $message,
                            'type'    => $type,
                            'url'     => route('installments.index'),
                        ]));

                        $installment->update([
                            'last_reminder_day' => $daysLate
                        ]);

                        $this->info("Sent reminder H+{$daysLate} to User: {$user->name}");
                    }
                });
            } catch (\Exception $e) {
                $this->error("Error processing Installment ID: {$installment->id} - {$e->getMessage()}");
            }
        }

        $this->info('Processing complete.');
    }
}
