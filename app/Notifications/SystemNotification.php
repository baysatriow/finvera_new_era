<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * ============================================================
     * CONSTRUCT — INISIALISASI DATA NOTIFIKASI
     * ============================================================
     * $data: [title, message, type, url]
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * ============================================================
     * VIA — CHANNEL NOTIFIKASI
     * ============================================================
     * Saat ini hanya menggunakan database.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * ============================================================
     * TO ARRAY — DATA YANG DISIMPAN KE DATABASE
     * ============================================================
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->data['title'],
            'message' => $this->data['message'],
            'type'    => $this->data['type'] ?? 'info',
            'url'     => $this->data['url'] ?? '#',
            'icon'    => $this->getIcon($this->data['type'] ?? 'info'),
        ];
    }

    /**
     * ============================================================
     * HELPER — ICON BERDASARKAN JENIS NOTIFIKASI
     * ============================================================
     */
    private function getIcon($type)
    {
        return match ($type) {
            'success' => 'check-circle',
            'warning' => 'exclamation-triangle',
            'danger'  => 'times-circle',
            default   => 'info-circle',
        };
    }
}
