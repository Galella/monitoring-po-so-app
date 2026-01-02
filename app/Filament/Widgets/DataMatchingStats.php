<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CmData\CmDataResource;
use App\Filament\Resources\CoinsData\CoinsDataResource;
use App\Models\CmData;
use App\Models\CoinsData;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DataMatchingStats extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('view_data_matching');
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        // 1. CM Data Query Scope
        $cmQuery = CmData::query();
        if ($user->isUserArea() && $user->area_id) {
            $cmQuery->where('area_id', $user->area_id);
        } elseif ($user->isUserWilayah() && $user->wilayah_id) {
            $cmQuery->whereHas('area', function ($q) use ($user) {
                $q->where('wilayah_id', $user->wilayah_id);
            });
        }

        // 2. COINS Data Query Scope
        $coinsQuery = CoinsData::query();
        if ($user->isUserWilayah() && $user->wilayah_id) {
            $coinsQuery->where('wilayah_id', $user->wilayah_id);
        } elseif ($user->isUserArea() && $user->area_id) {
            // User Area technically doesn't own COINS data directly, 
            // but usually belongs to a Wilayah.
            // Option: Show COINS in their Wilayah (Parent) or Hide?
            // "Show COINS in parent Wilayah" is safer context than global.
            if ($user->area && $user->area->wilayah_id) {
                $coinsQuery->where('wilayah_id', $user->area->wilayah_id);
            }
        }

        $totalCm = (clone $cmQuery)->count();
        $totalCoins = (clone $coinsQuery)->count();
        
        // Matched: Intersection
        // Note: For User Area, we only care about matches involving THEIR CM Data.
        // So we take the scoped $cmQuery and apply matching scope.
        $matched = (clone $cmQuery)->withMatchingCoins()->count();
        
        $unmatchedCm = (clone $cmQuery)->unmatched()->count();
        
        // Unmatched Coins: 
        // For User Area, they might not care about unmatched coins in the whole wilayah,
        // but strictly speaking, "Unmatched Coins" are those coins in the scope that have no CM match.
        $unmatchedCoins = (clone $coinsQuery)->unmatched()->count();

        $totalUnmatched = $unmatchedCm + $unmatchedCoins;

        $matchPercentage = $totalCm > 0 ? round(($matched / $totalCm) * 100, 1) : 0;

        return [
            Stat::make('Total CM', number_format($totalCm))
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->description('Lihat Data CM')
                ->descriptionIcon('heroicon-m-arrow-right')
                ->url(CmDataResource::getUrl('index')),

            Stat::make('Total COINS', number_format($totalCoins))
                ->icon('heroicon-o-circle-stack')
                ->color('primary')
                ->description('Lihat Data COINS')
                ->descriptionIcon('heroicon-m-arrow-right')
                ->url(CoinsDataResource::getUrl('index')),

            Stat::make('Match Rate', $matchPercentage . '%')
                ->description(number_format($matched) . ' Data matched')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([
                    0, 50, 75, $matchPercentage
                ]),

            Stat::make('Unmatched', number_format($totalUnmatched))
                ->description("CM: " . number_format($unmatchedCm) . " | COINS: " . number_format($unmatchedCoins))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),
        ];
    }
}
