<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * ============================================================
     * INDEX — TAMPILKAN SEMUA NOTIFIKASI USER
     * ============================================================
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * ============================================================
     * SHOW — DETAIL NOTIFIKASI & TANDAI SEBAGAI DIBACA
     * ============================================================
     */
    public function show($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return view('notifications.show', compact('notification'));
    }
}
