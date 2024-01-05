<?php

namespace Spatie\LaravelPackageTools\Traits;

use Carbon\Carbon;

trait RenewPassword
{

    public function needRenewPassword(): bool
    {
        return Carbon::parse($this->last_renew_password_at ?? $this->created_at)->addDays(config('filament-renew-password.renew_password_days_period')) < now();
    }

}