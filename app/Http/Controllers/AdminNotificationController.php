<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AdminNotificationController extends Controller
{
    /* ============================================================
     * INDEX — FORM + DAFTAR USER UNTUK TARGET NOTIFIKASI
     * ============================================================ */
    public function index()
    {
        $users = User::where('role', 'borrower')
            ->select('id', 'name', 'email')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.notifications.index', compact('users'));
    }

    /* ============================================================
     * STORE — PROSES KIRIM NOTIFIKASI (MANUAL / BROADCAST)
     * ============================================================ */
    public function store(Request $request)
    {
        /* ------------------------------
         * 1. VALIDASI INPUT
         * ------------------------------ */
        $request->validate([
            'title'            => 'required|string|max:255',
            'message'          => 'required|string',
            'type'             => 'required|string|in:info,success,warning,danger',
            'selected_users'   => 'required_without:is_broadcast|array',
            'selected_users.*' => 'exists:users,id',
            'is_broadcast'     => 'nullable'
        ], [
            'selected_users.required_without' => 'Silakan pilih minimal satu pengguna atau aktifkan Broadcast.',
            'title.required'                  => 'Judul notifikasi wajib diisi.',
            'message.required'                => 'Isi pesan wajib diisi.',
        ]);

        /* ------------------------------
         * 2. DATA NOTIFIKASI
         * ------------------------------ */
        $data = [
            'title'   => $request->title,
            'message' => $request->message,
            'type'    => $request->type,
            'url'     => route('dashboard'),
        ];

        /* ------------------------------
         * 3. TENTUKAN PENERIMA
         * ------------------------------ */
        if ($request->has('is_broadcast') && $request->is_broadcast == '1') {

            // Mode Broadcast
            $users = User::where('role', 'borrower')->get();
            $msg   = 'Notifikasi broadcast berhasil dikirim ke semua borrower (' . $users->count() . ' pengguna).';

        } else {

            // Mode Manual
            $users = User::whereIn('id', $request->selected_users)->get();
            $msg   = 'Notifikasi berhasil dikirim ke ' . $users->count() . ' pengguna terpilih.';
        }

        /* ------------------------------
         * 4. PROSES PENGIRIMAN
         * ------------------------------ */
        if ($users->isNotEmpty()) {
            Notification::send($users, new SystemNotification($data));
            return back()->with('success', $msg);
        }

        return back()->with('warning', 'Tidak ada penerima notifikasi yang ditemukan.');
    }
}
