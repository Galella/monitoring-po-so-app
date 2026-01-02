<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        ->components([
                        TextInput::make('name')
                            ->required()
                            ->inlineLabel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->inlineLabel()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->inlineLabel()
                            ->password()
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : $state)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visibleOn('create'),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : $state)
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed(false)
                            ->inlineLabel()
                            ->nullable()
                            ->visibleOn('edit')
                            ->label('New Password')
                            ->helperText('Leave blank to keep current password'),
                        Select::make('roles')
                            ->inlineLabel()
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                        Select::make('wilayah_id')
                            ->label('Wilayah')
                            ->relationship('wilayah', 'name')
                            ->searchable()
                            ->preload()
                            ->inlineLabel(),
                        Select::make('area_id')
                            ->label('Area')
                            ->relationship('area', 'name')
                            ->searchable()
                            ->preload()
                            ->inlineLabel(),

                ]);
    }
}