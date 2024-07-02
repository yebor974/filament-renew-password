<?php

namespace Yebor974\Filament\RenewPassword\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

trait RenewPassword
{
    public function needRenewPassword(): bool
    {
        $plugin = RenewPasswordPlugin::get();

        return
            (
                !is_null($plugin->getPasswordExpiresIn())
                && Carbon::parse($this->{$plugin->getTimestampColumn()})->addDays($plugin->getPasswordExpiresIn()) < now()
            ) || (
                $plugin->getForceRenewPassword()
                && $this->{$plugin->getForceRenewColumn()}
            );
    }
}
