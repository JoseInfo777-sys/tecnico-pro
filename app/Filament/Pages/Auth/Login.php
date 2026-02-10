<?php
namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Bienvenido a Infotech';
    }
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Correo Electrónico')
            ->email()
            ->required()
            ->autocomplete();
            //->focus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Tu Contraseña')
            ->password()
            ->required();
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Ingresar');
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->label('Recordar sesión');
    }
}