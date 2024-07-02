<?php

namespace Yebor974\Filament\RenewPassword\Pages\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;
use Yebor974\Filament\RenewPassword\RenewPasswordPlugin;

class RenewPassword extends SimplePage
{

    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-renew-password::pages.auth.renew-password';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        /** @var RenewPasswordContract $user */
        $user = Filament::auth()->user();

        if(
            ! in_array(RenewPasswordContract::class, class_implements($user))
            || !$user->needRenewPassword()
        ) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function renew()
    {
        /** @var Authenticatable & Model $user */
        $user = Filament::auth()->user();
        $data = $this->form->getState();

        $this->renewPassword($user, $data);

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        event(new PasswordReset($user));

        Notification::make()
            ->title(__('filament-renew-password::renew-password.notifications.title'))
            ->body(__('filament-renew-password::renew-password.notifications.body'))
            ->success()
            ->send();

        return redirect()->intended(Filament::getUrl());
    }

    public function renewPassword(Authenticatable & Model $record, array $data): Authenticatable & Model
    {
        $plugin = RenewPasswordPlugin::get();

        $record->password = $data['password'];

        if($plugin->getForceRenewPassword()) {
            $record->{$plugin->getForceRenewColumn()} = false;
        }

        if($plugin->getTimestampColumn()) {
            $record->{$plugin->getTimestampColumn()} = now();
        }

        $record->save();

        return $record;
    }

    public function form(Form $form): Form
    {
        return $form;
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getCurrentPasswordFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getConfirmationPasswordFormComponent()
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('currentPassword')
            ->label(__('filament-renew-password::renew-password.form.current-password.label'))
            ->password()
            ->required()
            ->rule('current_password:'.filament()->getAuthGuard());
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-renew-password::renew-password.form.password.label'))
            ->helperText(trans()->has('filament-renew-password::renew-password.form.password.helps') ? __('filament-renew-password::renew-password.form.password.helps') : null)
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
            ->required()
            ->rule(PasswordRule::default())
            ->different('currentPassword')
            ->same('passwordConfirmation');
    }

    protected function getConfirmationPasswordFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-renew-password::renew-password.form.password-confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->dehydrated(false)
            ->required();
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRenewFormAction(),
        ];
    }

    protected function getRenewFormAction(): Action
    {
        return Action::make('renew')
            ->label(__('filament-renew-password::renew-password.form.actions.renew.label'))
            ->submit('renew')
            ->keyBindings(['mod+s']);
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-renew-password::renew-password.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament-renew-password::renew-password.heading');
    }

}