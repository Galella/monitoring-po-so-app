<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use BackedEnum;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Details')
                    ->schema([
                        TextInput::make('causer_type')
                            ->label('Causer Type'),
                        TextInput::make('causer_id')
                            ->label('Causer ID'),
                        TextInput::make('subject_type')
                            ->label('Subject Type'),
                        TextInput::make('subject_id')
                            ->label('Subject ID'),
                        TextInput::make('description')
                            ->label('Description'),
                        Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn (?Activity $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                    ])
                    ->columns(2),
                Section::make('Properties')
                    ->schema([
                        KeyValue::make('properties.attributes')
                            ->label('New Data')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                        KeyValue::make('properties.old')
                            ->label('Old Data')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(function ($state, Activity $record) {
                        return $state ? class_basename($state) . ' #' . $record->subject_id : '-';
                    })
                    ->searchable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
