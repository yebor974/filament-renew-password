# Filament Renew Password Plugin

![Filament v3](https://img.shields.io/badge/Filament-v3--stable-success?logo=filament)
![Filament v4 beta support](https://img.shields.io/badge/Filament-v4--beta--support-orange?logo=filament)
![License](https://img.shields.io/github/license/yebor974/filament-renew-password)

The Filament Renew Password Plugin enhances Filament by prompting users to renew their passwords based on specified criteria.

Two default renewal processes are implemented:
- Recurring Renewal
- Force Renewal

You are free to add your own renewal criteria.

![Screenshot](https://julienboyer.re/assets/github/filament-renew-password/screenshot_1.png)

## Installation

1. Install the package using the composer command:

> ✅ **Filament v4** compatibility is available in **version 3.x** of this plugin (currently in beta).
>
> ```bash
> composer require yebor974/filament-renew-password
> ```
> 
> ✅ If you are using **Filament v3**, please install **version 2.x**:
> ```bash
> composer require yebor974/filament-renew-password:^2.0
> ```
>
> 🚧 **Version 3.x** is compatible with Filament v4 and is under active development. Some features might evolve.


2. Publish and run the migration, which adds new columns `last_renew_password_at` and `force_renew_password` to the users table.

```bash
php artisan vendor:publish --tag="filament-renew-password-migrations"
php artisan migrate
```

Alternatively, if you don't want to publish the migrations or already have columns in your database for such case, you can skip this step and customize the column name by using any of the configuration methods described in the [Configuration](#configuration) section below.

3. Optionally, you can publish translations files with:
```bash
php artisan vendor:publish --tag="filament-renew-password-translations"
```

4. Register the plugin in your panel provider:

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

### Renewal Processes

Two configurations are available and can be used simultaneously.

- Recurring renew process

By default, recurring renewal process is disabled.

To activate recurring renewal process, you have to call `passwordExpiresIn` and define the number of days of recurring:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
```

This activation automatically manages a last renewal timestamp column named `last_renew_password_at`. You can customize it with the `timestampColumn` function:

```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
    ->timestampColumn('your_custom_timestamp_column')
```

- Force renew process

The force renewal process can be useful for example when an administrator creates a user. You can send a temporary password to the new user and force them to renew their password at the first login.

By default, force renewal process is disabled.

To activate it, you have to call `forceRenewPassword` function:
```php
RenewPasswordPlugin::make()
    ->forceRenewPassword()
```

This activation automatically manages a force renew boolean column named `force_renew_password`. If you want to customize it, you can define with second param:
```php
RenewPasswordPlugin::make()
    ->forceRenewPassword(forceRenewColumn: 'your_custom_boolean_force_column')
```

If you dont want the recurring renewal process but only want the force renewal process with also timestamp column you can add it with:
```php
RenewPasswordPlugin::make()
    ->forceRenewPassword()
    ->timestampColumn('your_custom_timestamp_column')
```

> You can of course use both process with this configuration:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
    ->forceRenewPassword()
```

> And with columns customization:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
    ->forceRenewPassword(forceRenewColumn: 'your_custom_boolean_force_column')
    ->timestampColumn('your_custom_timestamp_column')
```

### Custom Renew Page

By default, `RenewPassword` simple page is used to ask user to renew it. You can custom it with:
```php
RenewPasswordPlugin::make()
    ->renewPage(CustomRenewPassword::class)
```

### Route URI

`'password/renew'` is the default route URI when being asked to change passwords. For translation purposes, you may want to customize this. You can do this with the following configuration:
```php
RenewPasswordPlugin::make()
    ->routeUri('wachtwoord/vernieuwen')
```

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
In this case, you will certainly need to customize the `RenewPassword` simple page described above.
