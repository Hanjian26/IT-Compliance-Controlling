<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        // ✅ Deteksi devtunnel dan paksa HTTPS agar cookie & token valid
        if (str_contains(config('app.url'), 'devtunnels.ms')) {
            URL::forceScheme('https');

            // 🔒 Penting: beri tahu Laravel bahwa request yang diterima adalah HTTPS
            if (request()->server->has('HTTP_X_FORWARDED_PROTO') &&
                request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
                request()->server->set('HTTPS', true);
            }
        }
    }
}