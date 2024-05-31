<?php

namespace Yebor974\Filament\RenewPassword;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Yebor974\Filament\RenewPassword\Middleware\RenewPasswordMiddleware;

class RenewPasswordPlugin implements Plugin
{
    protected ?int $passwordExpiresIn = null;

    protected ?string $timestampColumn = null;

    public function getId(): string
    {
        return 'filament-renew-password';
    }

    public function register(Panel $panel): void
    {
        $panel->authMiddleware([RenewPasswordMiddleware::class], true);
    }

    public function boot(Panel $panel): void
    {
        if(! $this->getPasswordExpiresIn()) {
            $this->passwordExpiresIn(config('filament-renew-password.renew_password_days_period'));
        }

        if(! $this->getTimestampColumn()) {
            $this->timestampColumn(config('filament-renew-password.renew_password_timestamp_column'));
        }
    }

    public function passwordExpiresIn(int $days): static
    {
        $this->passwordExpiresIn = $days;

        return $this;
    }

    public function getPasswordExpiresIn(): ?int
    {
        return $this->passwordExpiresIn;
    }

    public function timestampColumn(string $column): static
    {
        $this->timestampColumn = $column;

        return $this;
    }

    public function getTimestampColumn(): ?string
    {
        return $this->timestampColumn;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}