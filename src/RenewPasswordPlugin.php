<?php

namespace Yebor974\Filament\RenewPassword;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Yebor974\Filament\RenewPassword\Middleware\RenewPasswordMiddleware;

class RenewPasswordPlugin implements Plugin
{

    public function getId(): string
    {
        return 'Renew Password';
    }

    public function register(Panel $panel): void
    {
        $panel->authMiddleware([RenewPasswordMiddleware::class], true);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}