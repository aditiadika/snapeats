<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Entity;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterEntity extends RegisterTenant
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tenancy.register-entity';

    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Entity
    {
        $team = Entity::create($data);

        $team->entities()->attach(auth()->user());

        return $team;
    }
}
