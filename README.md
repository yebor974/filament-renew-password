# Filament Renew Password Plugin

The Filament Renew Password Plugin enhances Filament by prompting users to renew their passwords based on specified criteria.

![Screenshot](https://raw.githubusercontent.com/yebor974/filament-renew-password/main/docs/screenshots/screenshot_1.png)

## Installation

1. Install the package using the composer command:

```bash
composer require yebor974/filament-renew-password
```

2. Publish the associated vendor files and run the migration, which adds a new column `last_renew_password_at` to the users table.

```bash
php artisan vendor:publish
php artisan migrate
```

Alternatively, if you don't want to publish the migrations or already have a column in your database for such case, you can skip this step and customize the column name by using any of the configuration methods described in the [Configuration](#configuration) section below.

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

### Via Plugin Configuration
```php

// app/Providers/Filament/YourPanelServiceProvider.php

RenewPasswordPlugin::make()
    ->timestampColumn('password_changed_at')
    ->passwordExpiresIn(days: 30)
```

### Via Environment Variables
```env
// .env

FILAMENT_RENEW_PASSWORD_DAYS_PERIOD=30
FILAMENT_RENEW_PASSWORD_TIMESTAMP_COLUMN=last_renew_password_at
```

### Via Configuration File
```php
// config/filament-renew-password.php

return [
    'timestamp_column' => 'password_changed_at',
    'password_expires_in' => 30,
];
```

Any of the above methods will work. The plugin will use the configuration in the following order of priority: Plugin Configuration, Environment Variables, Configuration File.

## Usage

### Implementing the `RenewPasswordContract` Contract

1. Implement the `RenewPasswordContract` on your Authentication Model (User) and define the criteria for prompting password renewal in the `needRenewPassword` function.

> Example for a 90-day renewal period:
```php

class User extends Authenticatable implements RenewPasswordContract
{
    ... 
    
    public function needRenewPassword(): bool
    {
        return Carbon::parse($this->last_renew_password_at ?? $this->created_at)->addDays(90) < now();
    }
}
```

### Using the `RenewPassword` Trait

1. Alternatively, you can use the `RenewPassword` trait on your Authentication Model (User). By default, the trait uses the configured column and a 90-day renewal period. You can customize the column name and renewal period by [configuring the plugin](#configuration).

Enjoy ! :)