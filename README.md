# Filament plugin to add renew password on panel

This plugin permits to ask user to renew their password according to the last renew or other criteria.

## Install

You can install the package via composer command:

```bash
composer require yebor974/filament-renew-password
```

Publish associated vendor and launch migration. This adds new column `last-renew-password-at` in users table.

```php
php artisan vendor:publish
php artisan migrate
```

## Usage 

First, implement `RenewPasswordContract` on your Authenticate Model (User) and define criteria for asking renew password on `needRenewPassword` function

Example for each 90 days :
```php
public function needRenewPassword(): bool
{
    return Carbon::parse($this->last_renew_password_at ?? $this->created_at)->addDays(90) < now();
}
```

You can also use default Trait `RenewPassword` and customize the period in days with env named `FILAMENT_RENEW_PASSWORD_DAYS_PERIOD`
By default, it's configured for 90 days.

Next, register the plugin in your Filament's Panel :
```php
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
       ->plugin(new RenewPasswordPlugin());
}
```

And enjoy ! :)

![Screenshot](https://raw.githubusercontent.com/yebor974/filament-renew-password/main/docs/screenshots/screenshot_1.png)

