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

### Renewal Process

Two configurations are available and can be used at the same time.

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

## Migration V1 to V2

The version 2 no longer automatically enables the password renewal process. You must define the processes to use according to the documentation above. 
Additionally, there are no longer any associated configuration files or .env variables.

To migrate to V2 and enable the recurring renewal process, you need to call the `passwordExpiresIn` function during your plugin initialization with the renewal period in days:
```php
RenewPasswordPlugin::make()
    ->passwordExpiresIn(days: 30)
```

If you want to add the force renew process, you need to add the force boolean column to your authentication model (User) 
and declare it as shown in the [Configuration](#configuration) section above.
```php
$table->boolean('force_renew_password')->default(false);
```