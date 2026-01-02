<?php

namespace App\Filament\Resources\CmData;

use App\Imports\CmDataImport;
use App\Models\Area;
use App\Models\CmData;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Database\Eloquent\Builder;

class CmDataResource extends Resource
{
    protected static ?string $model = CmData::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Data Import';

    protected static ?string $modelLabel = 'CM Data';

    protected static ?string $pluralModelLabel = 'CM Data';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('area_id')
                    ->label('Area')
                    ->relationship('area', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('ppcw')
                    ->label('PPCW')
                    ->maxLength(255),
                TextInput::make('container')
                    ->label('Container')
                    ->maxLength(255),
                TextInput::make('seal')
                    ->label('Seal')
                    ->maxLength(255),
                TextInput::make('shipper')
                    ->label('Shipper')
                    ->maxLength(255),
                TextInput::make('consignee')
                    ->label('Consignee')
                    ->maxLength(255),
                TextInput::make('status')
                    ->label('Status')
                    ->maxLength(255),
                TextInput::make('commodity')
                    ->label('Commodity')
                    ->maxLength(255),
                TextInput::make('size')
                    ->label('Size')
                    ->maxLength(50),
                TextInput::make('weight')
                    ->label('Weight')
                    ->numeric(),
                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(3),
                TextInput::make('cm')
                    ->label('CM')
                    ->maxLength(255),
                DatePicker::make('atd')
                    ->label('ATD'),
                TextInput::make('no_order_coins')
                    ->label('No Order COINS')
                    ->maxLength(255),
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
                TextColumn::make('seal')
                    ->label('Seal')
                    ->searchable(),
                TextColumn::make('shipper')
                    ->label('Shipper')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('consignee')
                    ->label('Consignee')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('size')
                    ->label('Size'),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('area.name')
                    ->label('Area')
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
                SelectFilter::make('area')
                    ->relationship('area', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => !auth()->user()->isUserArea()),
                SelectFilter::make('status')
                    ->options(fn () => CmData::distinct()->pluck('status', 'status')->filter()->toArray()),
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
                    ->url(asset('templates/template_cm_data.csv'))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()->can('import_cm_data')),
                Action::make('import')
                    ->label('Import Excel')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->color('success')
                    ->visible(fn () => auth()->user()->can('import_cm_data'))
                    ->modal()
                    ->modalHeading('Import CM Data')
                    ->modalDescription('Download template terlebih dahulu jika belum punya. Kolom wajib: cm, container')
                    ->modalWidth('md')
                    ->schema([
                        Select::make('area_id')
                            ->label('Area')
                            ->options(Area::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->default(fn () => auth()->user()->area_id)
                            ->disabled(fn () => auth()->user()->isUserArea())
                            ->dehydrated(), // Ensure value is passed even if disabled
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
                        $areaId = $data['area_id'];

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

                            $importer = new CmDataImport($areaId);
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
            'index' => \App\Filament\Resources\CmData\Pages\ListCmData::route('/'),
            'edit' => \App\Filament\Resources\CmData\Pages\EditCmData::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user && $user->isUserArea() && $user->area_id) {
            $query->where('area_id', $user->area_id);
        }

        return $query;
    }
}
