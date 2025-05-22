<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        Gate::define('view-any-transaction', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('view-event-transaction', function ($user, Event $event) {
            return $user->role === 'admin' 
            || ($user->role === 'organizer' && $user->id === $event->organizer->user_id);
        });

        Gate::define('view-transaction', function ($user, Transaction $transaction) {
            return $user->role === 'admin' 
            || ($user->role === 'organizer' && $user->id === $transaction->event->organizer->user_id);
        });
    }
}
