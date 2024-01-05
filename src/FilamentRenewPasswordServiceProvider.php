<?php

namespace Yebor974\Filament\RenewPassword;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Yebor974\Filament\RenewPassword\Pages\Auth\RenewPassword;

class FilamentRenewPasswordServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-renew-password')
            ->hasConfigFile()
            ->hasMigration('add_renew_password_on_users_table')
            ->hasRoute('web')
            ->hasTranslations()
            ->hasViews()
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('yebor974/filament-renew-password');
            });
    }

    public function packageBooted()
    {
        Livewire::component('yebor974.filament.renew-password.pages.auth.renew-password',RenewPassword::class);

        parent::packageBooted();
    }
}