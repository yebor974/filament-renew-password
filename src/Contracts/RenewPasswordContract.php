<?php

namespace Yebor974\Filament\RenewPassword\Contracts;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable;
interface RenewPasswordContract extends Authenticatable, Authorizable
{

    public function needRenewPassword(): bool;

}