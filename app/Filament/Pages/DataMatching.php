<?php

namespace App\Filament\Pages;

use App\Models\Area;
use App\Models\CmData;
use App\Models\CoinsData;
use App\Models\Wilayah;
use BackedEnum;
use Filament\Actions\Action;
// use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use UnitEnum;
use Maatwebsite\Excel\Facades\Excel;

class DataMatching extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static ?string $navigationLabel = 'Data Matching';

    protected static ?string $title = 'Data Matching';

    protected static UnitEnum|string|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    public function getView(): string
    {
        return 'filament.pages.data-matching';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\DataMatchingStats::class,
        ];
    }

    #[Url]
    public string $activeTab = 'matched';

    public function getTabs(): array
    {
        return [
            'matched' => [
                'label' => 'Matched',
                'icon' => Heroicon::CheckCircle,
                'badge' => $this->getMatchedCount(),
                'badgeColor' => 'success',
            ],
            'cm_only' => [
                'label' => 'CM Only (Unmatched)',
                'icon' => Heroicon::ExclamationTriangle,
                'badge' => $this->getUnmatchedCmCount(),
                'badgeColor' => 'warning',
            ],
            'coins_only' => [
                'label' => 'COINS Only (Unmatched)',
                'icon' => Heroicon::ExclamationTriangle,
                'badge' => $this->getUnmatchedCoinsCount(),
                'badgeColor' => 'warning',
            ],
        ];
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    protected function getMatchedCount(): int
    {
        return $this->applyRbac(CmData::withMatchingCoins())->count();
    }

    protected function getUnmatchedCmCount(): int
    {
        return $this->applyRbac(CmData::unmatched())->count();
    }

    protected function getUnmatchedCoinsCount(): int
    {
        return CoinsData::unmatched()->count();
    }

    public function getTotalCmCount(): int
    {
        return CmData::count();
    }

    public function getTotalCoinsCount(): int
    {
        return CoinsData::count();
    }

    public function getMatchPercentage(): float
    {
        $totalCm = $this->getTotalCmCount();
        if ($totalCm === 0) return 0;
        return round(($this->getMatchedCount() / $totalCm) * 100, 1);
    }

    public function table(Table $table): Table
    {
        return match ($this->activeTab) {
            'matched' => $this->matchedTable($table),
            'cm_only' => $this->cmOnlyTable($table),
            'coins_only' => $this->coinsOnlyTable($table),
            default => $this->matchedTable($table),
        };
    }

    protected function matchedTable(Table $table): Table
    {
        return $table
            ->query($this->applyRbac(CmData::query()->withMatchingCoins()->with(['area'])))
            ->columns([
                TextColumn::make('cm')
                    ->label('CM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('container')
                    ->label('Container')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('seal')
                    ->label('Seal (CM)')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('shipper')
                    ->label('Shipper')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->shipper)
                    ->toggleable(),
                TextColumn::make('consignee')
                    ->label('Consignee')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->consignee)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color('success'),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->relationship('area', 'name')
                    ->label('Area')
                    ->searchable()
                    ->preload(),
                Filter::make('atd')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Detail Data Matched')
                    ->modalWidth('4xl')
                    ->infolist([
                        Section::make('Data CM')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('cm')->label('CM')->weight('bold'),
                                TextEntry::make('container')->label('Container'),
                                TextEntry::make('seal')->label('Seal'),
                                TextEntry::make('shipper')->label('Shipper'),
                                TextEntry::make('consignee')->label('Consignee'),
                                TextEntry::make('status')->label('Status')->badge(),
                                TextEntry::make('commodity')->label('Commodity'),
                                TextEntry::make('size')->label('Size'),
                                TextEntry::make('weight')->label('Weight'),
                                TextEntry::make('atd')->label('ATD')->date('d M Y'),
                                TextEntry::make('area.name')->label('Area'),
                                TextEntry::make('importer.name')->label('Imported By'),
                            ]),
                        Section::make('Data COINS (Matched)')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('matchingCoins.order')->label('Order'),
                                TextEntry::make('matchingCoins.customer')->label('Customer'),
                                TextEntry::make('matchingCoins.kereta')->label('Kereta'),
                                TextEntry::make('matchingCoins.so')->label('SO'),
                                TextEntry::make('matchingCoins.payment')->label('Payment'),
                                TextEntry::make('matchingCoins.nominal')
                                    ->label('Nominal')
                                    ->money('IDR'),
                                TextEntry::make('matchingCoins.stasiun_asal')->label('Stasiun Asal'),
                                TextEntry::make('matchingCoins.stasiun_tujuan')->label('Stasiun Tujuan'),
                                TextEntry::make('matchingCoins.wilayah.name')->label('Wilayah'),
                            ]),
                    ]),
            ])
            ->striped()
            ->defaultSort('atd', 'desc')
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('Belum ada data yang cocok')
            ->emptyStateDescription('Import data CM dan COINS untuk melihat hasil matching.');
    }

    protected function cmOnlyTable(Table $table): Table
    {
        return $table
            ->query($this->applyRbac(CmData::query()->unmatched()->with('area')))
            ->columns([
                TextColumn::make('cm')
                    ->label('CM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('warning')
                    ->weight('bold'),
                TextColumn::make('container')
                    ->label('Container')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('warning'),
                TextColumn::make('seal')
                    ->label('Seal')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('shipper')
                    ->label('Shipper')
                    ->limit(20)
                    ->toggleable(),
                TextColumn::make('consignee')
                    ->label('Consignee')
                    ->limit(20)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('area.name')
                    ->label('Area')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('area')
                    ->relationship('area', 'name')
                    ->label('Area')
                    ->searchable()
                    ->preload(),
                Filter::make('atd')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Detail CM Data')
                    ->modalWidth('3xl')
                    ->infolist([
                        Section::make('Data CM')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('cm')->label('CM')->weight('bold'),
                                TextEntry::make('container')->label('Container'),
                                TextEntry::make('seal')->label('Seal'),
                                TextEntry::make('shipper')->label('Shipper'),
                                TextEntry::make('consignee')->label('Consignee'),
                                TextEntry::make('status')->label('Status'),
                                TextEntry::make('commodity')->label('Commodity'),
                                TextEntry::make('size')->label('Size'),
                                TextEntry::make('weight')->label('Weight'),
                                TextEntry::make('keterangan')->label('Keterangan'),
                                TextEntry::make('atd')->label('ATD')->date('d M Y'),
                                TextEntry::make('area.name')->label('Area'),
                            ]),
                    ]),
                \Filament\Actions\EditAction::make()
                    ->url(fn (CmData $record) => route('filament.admin.resources.cm-data.edit', $record)),
            ])
            ->striped()
            ->defaultSort('atd', 'desc')
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('Semua data CM sudah cocok!')
            ->emptyStateDescription('Tidak ada data CM yang belum ada di COINS.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle);
    }

    protected function coinsOnlyTable(Table $table): Table
    {
        return $table
            ->query(CoinsData::query()->unmatched()->with('wilayah'))
            ->columns([
                TextColumn::make('cm')
                    ->label('CM')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('warning')
                    ->weight('bold'),
                TextColumn::make('container')
                    ->label('Container')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('warning'),
                TextColumn::make('order')
                    ->label('Order')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('customer')
                    ->label('Customer')
                    ->limit(20)
                    ->toggleable(),
                TextColumn::make('kereta')
                    ->label('Kereta')
                    ->toggleable(),
                TextColumn::make('so')
                    ->label('SO')
                    ->searchable(),
                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('atd')
                    ->label('ATD')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('wilayah.name')
                    ->label('Wilayah')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('wilayah')
                    ->relationship('wilayah', 'name')
                    ->label('Wilayah')
                    ->searchable()
                    ->preload(),
                Filter::make('atd')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('atd', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Detail COINS Data')
                    ->modalWidth('4xl')
                    ->infolist([
                        Section::make('Informasi Utama')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('cm')->label('CM')->weight('bold'),
                                TextEntry::make('container')->label('Container'),
                                TextEntry::make('seal')->label('Seal'),
                                TextEntry::make('order')->label('Order'),
                                TextEntry::make('customer')->label('Customer'),
                                TextEntry::make('kereta')->label('Kereta'),
                            ]),
                        Section::make('Stasiun & Gudang')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('stasiun_asal')->label('Stasiun Asal'),
                                TextEntry::make('stasiun_tujuan')->label('Stasiun Tujuan'),
                                TextEntry::make('gudang_asal')->label('Gudang Asal'),
                                TextEntry::make('gudang_tujuan')->label('Gudang Tujuan'),
                            ]),
                        Section::make('SO & Payment')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('so')->label('SO'),
                                TextEntry::make('submit_so')->label('Submit SO')->date('d M Y'),
                                TextEntry::make('payment')->label('Payment'),
                                TextEntry::make('nominal')->label('Nominal')->money('IDR'),
                                TextEntry::make('atd')->label('ATD')->date('d M Y'),
                                TextEntry::make('wilayah.name')->label('Wilayah'),
                            ]),
                    ]),
                \Filament\Actions\EditAction::make()
                    ->url(fn (CoinsData $record) => route('filament.admin.resources.coins-data.edit', $record)),
            ])
            ->striped()
            ->defaultSort('atd', 'desc')
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('Semua data COINS sudah cocok!')
            ->emptyStateDescription('Tidak ada data COINS yang belum ada di CM.')
            ->emptyStateIcon(Heroicon::OutlinedCheckCircle);
    }
    protected function applyRbac(Builder $query): Builder
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user && $user->isUserArea() && $user->area_id) {
            $query->where('area_id', $user->area_id);
        }

        return $query;
    }
}
