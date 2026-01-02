<?php

namespace App\Filament\Widgets;

use App\Models\CoinsData;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonitoringPoStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('view_monitoring_po');
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // Base Query: Matched Data
        $query = CoinsData::query()->withMatchingCm();

        // Scope by Role
        if ($user->isUserArea() && $user->area_id) {
            // Only show COINS that match CMs from this specific Area
            $query->whereExists(function ($subquery) use ($user) {
                $subquery->select(\DB::raw(1))
                    ->from('cm_data')
                    ->whereColumn('cm_data.cm', 'coins_data.cm')
                    ->whereColumn('cm_data.container', 'coins_data.container')
                    ->where('cm_data.area_id', $user->area_id);
            });
        } elseif ($user->isUserWilayah() && $user->wilayah_id) {
            $query->where('wilayah_id', $user->wilayah_id);
        }
        
        $totalMatched = (clone $query)->count();

        // 1. SO Submitted Manual: Nilai 'so' == 'Manual'
        $manualSo = (clone $query)->where('so', 'Manual')->count();

        // 2. SO Not Submitted: 'so' kosong OR 'so' == 'Not Submitted'
        $belumSo = (clone $query)->where(fn($q) => 
            $q->whereNull('so')
              ->orWhere('so', '')
              ->orWhere('so', 'Not Submitted')
        )->count();

        // 3. SO Submitted: Ada isinya, TAPI bukan 'Manual' dan bukan 'Not Submitted'
        $sudahSo = (clone $query)
            ->whereNotNull('so')
            ->where('so', '!=', '')
            ->where('so', '!=', 'Manual')
            ->where('so', '!=', 'Not Submitted')
            ->count();

        // Calculate progress percentage (Submitted vs Total)
        $progress = $totalMatched > 0 ? round(($sudahSo / $totalMatched) * 100, 1) : 0;

        return [
            Stat::make('SO Submitted', number_format($sudahSo))
                ->description("Progress: {$progress}%")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                 ->chart([
                    0, $progress
                ]),

            Stat::make('SO Not Submitted', number_format($belumSo))
                ->description('Menunggu / Not Submitted')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('SO Submitted Manual', number_format($manualSo))
                ->description('Status: Manual')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),
        ];
    }
}
