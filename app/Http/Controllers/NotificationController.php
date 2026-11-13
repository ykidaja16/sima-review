<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tandai notifikasi sebagai sudah dibaca lalu redirect ke tujuan
     */
    public function read(Request $request, string $key): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            NotificationService::markAsRead($user->id, $key);
        }

        $redirect = $request->query('redirect', route('dashboard'));

        // Pastikan url redirect aman (hanya internal atau relative path)
        if (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://')) {
            $appUrl = config('app.url');
            $parsedRedirect = parse_url($redirect, PHP_URL_HOST);
            $parsedApp = parse_url($appUrl, PHP_URL_HOST);

            if ($parsedRedirect && $parsedApp && $parsedRedirect !== $parsedApp && $parsedRedirect !== 'localhost' && $parsedRedirect !== '127.0.0.1') {
                $redirect = route('dashboard');
            }
        }

        return redirect($redirect);
    }

    /**
     * Tandai semua notifikasi aktif sebagai sudah dibaca (kecuali FTKP)
     */
    public function markAllAsRead(): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $notifications = NotificationService::getNotificationsForUser($user);
            $keys = array_column($notifications, 'id');
            NotificationService::markAllAsRead($user->id, $keys);
        }

        return back();
    }
}

