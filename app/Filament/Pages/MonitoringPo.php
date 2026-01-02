<?php

namespace App\Filament\Pages;

use App\Models\CoinsData;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

use BackedEnum;
use UnitEnum;

use App\Filament\Widgets\MonitoringPoStats;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonitoringPoExport;

class MonitoringPo extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Monitoring PO';

    protected static ?string $title = 'Monitoring SO';

    protected static ?string $slug = 'monitoring-po';
    
    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_monitoring_po');
    }

    protected string $view = 'filament.pages.monitoring-po';

    protected function getHeaderWidgets(): array
    {
        return [
            MonitoringPoStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()->can('export_monitoring_po'))
                ->action(function () {
                     return Excel::download(new MonitoringPoExport, 'monitoring-po-' . now()->format('Y-m-d') . '.xlsx');
                }),
        ];
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(CoinsData::query()->withMatchingCm())
            ->columns([
                // ... existing columns
                TextColumn::make('no_po')
                    ->label('No PO')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cm')
                    ->label('No CM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('container')
                    ->label('Container')
                    ->searchable(),
                TextColumn::make('seal')
                    ->label('Seal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('so')
                    ->label('SO')
                    ->searchable(),
                TextColumn::make('submit_so')
                    ->label('Submit SO')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nominal_ppn')
                    ->label('Nominal PPN')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_so')
                    ->label('Status SO')
                    ->options([
                        'submitted' => 'SO Submitted',
                        'not_submitted' => 'SO Not Submitted (Menunggu)',
                        'manual' => 'SO Manual',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'];
                        if ($value === 'submitted') {
                            return $query->whereNotNull('so')
                                ->where('so', '!=', '')
                                ->where('so', '!=', 'Manual')
                                ->where('so', '!=', 'Not Submitted');
                        }
                        if ($value === 'not_submitted') {
                            return $query->where(fn ($q) => 
                                $q->whereNull('so')
                                  ->orWhere('so', '')
                                  ->orWhere('so', 'Not Submitted')
                            );
                        }
                        if ($value === 'manual') {
                            return $query->where('so', 'Manual');
                        }
                        return $query;
                    }),
            ])
            ->actions([
                EditAction::make('update')
                    ->label('Update SO')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        TextInput::make('so')
                            ->label('Nomor SO')
                            ->required()
                            ->maxLength(255)
                            ->afterStateHydrated(function ($component, $state) {
                                if (blank($state) || $state === 'Not Submitted') {
                                    $component->state('Manual');
                                }
                            }),
                        DatePicker::make('submit_so')
                            ->label('Tanggal Submit SO')
                            ->afterStateHydrated(function ($component, $state) {
                                if (blank($state)) {
                                    $component->state(now()->format('Y-m-d'));
                                }
                            }),
                    ])
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                BulkAction::make('set_manual_so')
                    ->label('Set SO Manual')
                    ->icon('heroicon-o-pencil-square')
                    ->requiresConfirmation()
                    ->form([
                        DatePicker::make('submit_so_date')
                            ->label('Tanggal Submit SO')
                            ->required()
                            ->default(now()),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $records->each(function ($record) use ($data) {
                            $record->update([
                                'so' => 'Manual',
                                'submit_so' => $data['submit_so_date'],
                            ]);
                        });

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Status SO berhasil diubah menjadi Manual.')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}
