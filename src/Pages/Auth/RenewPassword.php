<?php

namespace Yebor974\Filament\RenewPassword\Pages\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Support\Htmlable;
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
        $data = $this->form->getState();

        $user = Filament::auth()->user();

        $timestampColumn = RenewPasswordPlugin::get()->getTimestampColumn();

        $hashPassword = Hash::make($data['password']);
        $user->forceFill([
            'password' => $hashPassword,
            $timestampColumn => now()
        ])->save();

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $hashPassword,
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
                        TextInput::make('currentPassword')
                            ->label(__('filament-renew-password::renew-password.form.current-password.label'))
                            ->password()
                            ->required()
                            ->rule('current_password:'.filament()->getAuthGuard()),
                        TextInput::make('password')
                            ->label(__('filament-renew-password::renew-password.form.password.label'))
                            ->password()
                            ->required()
                            ->rules(['different:data.currentPassword', PasswordRule::default()]),
                        TextInput::make('PasswordConfirmation')
                            ->label(__('filament-renew-password::renew-password.form.password-confirmation.label'))
                            ->password()
                            ->required()
                            ->same('password'),
                    ])
                    ->statePath('data'),
            ),
        ];
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
            ->submit('renew');
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