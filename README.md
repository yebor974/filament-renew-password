# Filament Renew Password Plugin

The Filament Renew Password Plugin enhances Filament by prompting users to renew their passwords based on specified criteria.

![Screenshot](https://raw.githubusercontent.com/yebor974/filament-renew-password/main/docs/screenshots/screenshot_1.png)

## Installation

1. Install the package using the composer command:

```bash
composer require yebor974/filament-renew-password
```

2. Publish the associated vendor files and run the migration, which adds new columns `last_renew_password_at` and `force_renew_password` to the users table.

```bash
php artisan vendor:publish
php artisan migrate
```

Alternatively, if you don't want to publish the migrations or already have columns in your database for such case, you can skip this step and customize the column name by using any of the configuration methods described in the [Configuration](#configuration) section below.

3. Register the plugin in your panel provider:

```php
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
       ->plugin(new RenewPasswordPlugin());
}
```

## Configuration
Filament Renew Password Plugin is designed to work out of the box with minimal configuration. However, you can customize the plugin by publishing the configuration file, changing the environment variables or using the plugin object to override the default settings.

Two configurations are available and can be used at the same time.

1. Recurring renew process

By default, recurring renewal is activated. You can disable it by calling the `passwordExpiresIn` function with a null value:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(null)
```

Alternatively, you can customize the number of days (default is 90 days) by calling the `passwordExpiresIn` function:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
```
or:
- by setting `.env` file variable `FILAMENT_RENEW_PASSWORD_DAYS_PERIOD`
- by setting `renew_password_days_period` attribute in config file `filament-renew-password.php`

By default, the last renewal timestamp column is named last_renew_password_at. If you want to customize it, you can set it using the `timestampColumn` function:
```php
RenewPasswordPlugin::make()
    ->timestampColumn('your_custom_timestamp_column')
    ->passwordExpiresIn(days: 30)
```
or: 
- by setting `.env` file variable `FILAMENT_RENEW_PASSWORD_TIMESTAMP_COLUMN`
- by setting `renew_password_timestamp_column` in config file `filament-renew-password.php`

2. Force renew process

The force renew process can be useful when an administrator creates a user, for example. You can send a temporary password to the new user and force them to renew their password at the first login.

If you want to use the force renew process, you can set it with:
```php
RenewPasswordPlugin::make()
    ->forceRenewPassword()
```

By default, the force renew boolean column is named `force_renew_password`. If you want to customize it, you can define it with the `forceRenewColumn` function:
```php
RenewPasswordPlugin::make()
    ->forceRenewColumn('your_custom_force_column')
    ->forceRenewPassword()
```
or:
- by setting `.env` file variable `FILAMENT_RENEW_PASSWORD_FORCE_COLUMN`
- by setting `renew_password_force_column` in config file `filament-renew-password.php`

Any of the above methods will work. The plugin will use the configuration in the following order of priority: Plugin Configuration, Environment Variables, Configuration File.

## Usage

Implement the `RenewPasswordContract` on your Authentication Model (User) and define the criteria for prompting password renewal in the `needRenewPassword` function.

- Default Trait

You can use the `RenewPassword` trait on your Authentication Model (User).

```php
class User extends Authenticatable implements RenewPasswordContract
{
    use RenewPassword;
}
```

This trait manages recurring renew if activated and/or force renew if activated:
```php
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
```
- Custom criteria

You can make your own criteria by implement `needRenewPassword` function on your Authentication Model (User).

## Migration V1 to V2

If you have installed version 1 and want to upgrade to version 2 with the force renew process, you need to add a column to your authentication model (User) and declare it as shown in the [Configuration](#configuration) section above.