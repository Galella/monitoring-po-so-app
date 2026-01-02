<?php

namespace App\Filament\Resources\CoinsData;

use App\Imports\CoinsDataImport;
use App\Models\CoinsData;
use App\Models\Wilayah;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class CoinsDataResource extends Resource
{
    protected static ?string $model = CoinsData::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static UnitEnum|string|null $navigationGroup = 'Data Import';

    protected static ?string $modelLabel = 'COINS Data';

    protected static ?string $pluralModelLabel = 'COINS Data';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama')
                    ->columns(3)
                    ->schema([
                        Select::make('wilayah_id')
                            ->label('Wilayah')
                            ->relationship('wilayah', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('cm')
                            ->label('CM')
                            ->maxLength(255),
                        TextInput::make('order')
                            ->label('Order')
                            ->maxLength(255),
                        TextInput::make('container')
                            ->label('Container')
                            ->maxLength(255),
                        TextInput::make('seal')
                            ->label('Seal')
                            ->maxLength(255),
                        TextInput::make('no_po')
                            ->label('No PO')
                            ->maxLength(255),
                        TextInput::make('kereta')
                            ->label('Kereta')
                            ->maxLength(255),
                        DatePicker::make('atd')
                            ->label('ATD'),
                        TextInput::make('customer')
                            ->label('Customer')
                            ->maxLength(255),
                    ]),
                Section::make('Stasiun & Gudang')
                    ->columns(2)
                    ->schema([
                        TextInput::make('stasiun_asal')
                            ->label('Stasiun Asal'),
                        TextInput::make('stasiun_tujuan')
                            ->label('Stasiun Tujuan'),
                        TextInput::make('gudang_asal')
                            ->label('Gudang Asal'),
                        TextInput::make('gudang_tujuan')
                            ->label('Gudang Tujuan'),
                    ]),
                Section::make('SO & Payment')
                    ->columns(3)
                    ->schema([
                        TextInput::make('so')
                            ->label('SO'),
                        DatePicker::make('submit_so')
                            ->label('Submit SO'),
                        TextInput::make('payment')
                            ->label('Payment'),
                        TextInput::make('nominal')
                            ->label('Nominal')
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cm')
                    ->label('CM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('container')
                    ->label('Container')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Order')
                    ->searchable(),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('kereta')
                    ->label('Kereta')
                    ->searchable(),
                TextColumn::make('so')
                    ->label('SO')
                    ->searchable(),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('wilayah.name')
                    ->label('Wilayah')
                    ->sortable(),
                TextColumn::make('importer.name')
                    ->label('Imported By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Imported At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('wilayah')
                    ->relationship('wilayah', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !auth()->user()->isUserWilayah()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('downloadTemplate')
                    ->label('Download Template')
                    ->icon(Heroicon::OutlinedDocumentArrowDown)
                    ->color('gray')
                    ->url(asset('templates/template_coins_data.csv'))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()->can('import_coins_data')),
                Action::make('import')
                    ->label('Import Excel')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->color('success')
                    ->visible(fn () => auth()->user()->can('import_coins_data'))
                    ->modal()
                    ->modalHeading('Import COINS Data')
                    ->modalDescription('Download template terlebih dahulu jika belum punya. Kolom wajib: cm, container')
                    ->modalWidth('md')
                    ->schema([
                        Select::make('wilayah_id')
                            ->label('Wilayah')
                            ->options(Wilayah::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->default(fn () => auth()->user()->wilayah_id)
                            ->disabled(fn () => auth()->user()->isUserWilayah())
                            ->dehydrated(),
                        FileUpload::make('file')
                            ->label('File Excel')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv',
                            ])
                            ->storeFiles(false)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $file = $data['file'];
                        $wilayahId = $data['wilayah_id'];

                        try {
                            // With storeFiles(false), we get TemporaryUploadedFile
                            if ($file instanceof TemporaryUploadedFile) {
                                $filePath = $file->getRealPath();
                            } elseif (is_array($file) && !empty($file)) {
                                $firstFile = reset($file);
                                if ($firstFile instanceof TemporaryUploadedFile) {
                                    $filePath = $firstFile->getRealPath();
                                } else {
                                    throw new \Exception('Invalid file format');
                                }
                            } else {
                                throw new \Exception('No file uploaded');
                            }

                            $importer = new CoinsDataImport($wilayahId);
                            Excel::import($importer, $filePath);

                            $inserted = $importer->getInsertedCount();
                            $updated = $importer->getUpdatedCount();
                            $skipped = $importer->getSkippedCount();

                            Notification::make()
                                ->title('Import Berhasil')
                                ->body("Data baru: {$inserted}, Diupdate: {$updated}, Dilewati: {$skipped}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => \App\Filament\Resources\CoinsData\Pages\ListCoinsData::route('/'),
            'edit' => \App\Filament\Resources\CoinsData\Pages\EditCoinsData::route('/{record}/edit'),
        ];
    }
}
