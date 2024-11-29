<?php

namespace Yebor974\Filament\RenewPassword\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
interface RenewPasswordContract
{
    public function needRenewPassword(): bool;
}
