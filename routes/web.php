<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::name('filament.')->group(function () {
    foreach (Filament::getPanels() as $panel) {
        $domains = $panel->getDomains();

        foreach ((empty($domains) ? [null] : $domains) as $domain) {
            Route::domain($domain)
                ->middleware($panel->getMiddleware())
                ->name($panel->getId() . '.')
                ->prefix($panel->getPath())
                ->group(function () use ($panel) {
                    Route::get('password/renew', \Yebor974\Filament\RenewPassword\Pages\Auth\RenewPassword::class)->name('auth.password.renew');
                });
        }
    }
});
