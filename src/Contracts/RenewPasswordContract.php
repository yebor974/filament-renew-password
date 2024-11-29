<?php

namespace Yebor974\Filament\RenewPassword\Contracts;

interface RenewPasswordContract
{
    public function needRenewPassword(): bool;
}
