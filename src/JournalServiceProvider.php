<?php

namespace Layman\LaravelJournal;

use Illuminate\Support\ServiceProvider;
use Layman\LaravelJournal\Middleware\Authenticate;
use Layman\LaravelJournal\Middleware\RedirectIfAuthenticated;
use Layman\LaravelJournal\Models\JournalUser;

class JournalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        config(['auth.providers.users.model' => JournalUser::class]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('journal.auth', Authenticate::class);
        $router->aliasMiddleware('journal.guest', RedirectIfAuthenticated::class);
        $this->loadViewsFrom(__DIR__ . '/Views', 'journal');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/journal.php');

        $this->publishes([
            __DIR__ . '/../config/journal.php' => config_path('journal.php'),
        ], 'journal');
    }
}
