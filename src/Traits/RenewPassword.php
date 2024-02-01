<?php

namespace Yebor974\Filament\RenewPassword\Traits;

use Carbon\Carbon;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

trait RenewPassword
{
    public function needRenewPassword(): bool
    {
        $plugin = RenewPasswordPlugin::get();

        return Carbon::parse($this->{$plugin->getTimestampColumn()} ?? $this->created_at)->addDays($plugin->getPasswordExpiresIn()) < now();
    }
}
