<?php

namespace App\Providers;

use App\Services\NotificationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share notifikasi ke layout utama
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $notifications = $user ? NotificationService::getNotificationsForUser($user) : [];
            $unreadCount = count(array_filter($notifications, fn($n) => empty($n['is_read'])));

            $view->with('appNotifications', $notifications);
            $view->with('unreadNotifCount', $unreadCount);
        });
    }
}
