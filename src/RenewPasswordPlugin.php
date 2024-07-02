<?php

namespace Yebor974\Filament\RenewPassword;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Yebor974\Filament\RenewPassword\Middleware\RenewPasswordMiddleware;

class RenewPasswordPlugin implements Plugin
{
    protected ?int $passwordExpiresIn = null;

    protected bool $forceRenewPassword = false;

    protected ?string $timestampColumn = null;

    protected ?string $forceRenewColumn = null;

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
        //
    }

    public function timestampColumn(string $timestampColumn = 'last_renew_password_at'): static
    {
        $this->timestampColumn = $timestampColumn;

        return $this;
    }

    public function getTimestampColumn(): ?string
    {
        return $this->timestampColumn;
    }

    public function passwordExpiresIn(int $days = null): static
    {
        $this->passwordExpiresIn = $days;
        if(!$this->timestampColumn) {
            $this->timestampColumn();
        }

        return $this;
    }

    public function getPasswordExpiresIn(): ?int
    {
        return $this->passwordExpiresIn;
    }

    public function forceRenewPassword(bool $force = true, string $forceRenewColumn = 'force_renew_password'): static
    {
        $this->forceRenewPassword = $force;
        $this->forceRenewColumn = $forceRenewColumn;

        return $this;
    }

    public function getForceRenewPassword(): ?int
    {
        return $this->forceRenewPassword;
    }

    public function getForceRenewColumn(): ?string
    {
        return $this->forceRenewColumn;
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