<?php

namespace App\Filament\Widgets;

use App\Models\CmData;
use App\Models\CoinsData;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class UnmatchedRatioChart extends ChartWidget
{
    protected ?string $heading = 'Data Matching Ratio';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        
        // Scope CM Data based on RBAC (Area)
        $cmQuery = CmData::query();
        if ($user && $user->isUserArea() && $user->area_id) {
            $cmQuery->where('area_id', $user->area_id);
        }

        $matchedCount = (clone $cmQuery)->withMatchingCoins()->count();
        $unmatchedCmCount = (clone $cmQuery)->unmatched()->count();
        
        // Coins Data - tricky effectively applying RBAC if user is Area based but Coins has Wilayah.
        // Assuming user wants to see global or relevant coins.
        // For simplicity and to match DataMatching page logic where Coins Unmatched is just CoinsData::unmatched()->count() (see line 101 of DataMatching.php)
        // But wait, line 101 of DataMatching.php DOES NOT apply RBAC to coins?
        // Ah, CoinsData::unmatched() is used.
        // Let's stick to what DataMatching page does.
        
        $unmatchedCoinsCount = CoinsData::unmatched()->count();

        return [
            'datasets' => [
                [
                    'label' => 'Data records',
                    'data' => [$matchedCount, $unmatchedCmCount, $unmatchedCoinsCount],
                    'backgroundColor' => [
                        '#4ade80', // green-400 (Matched)
                        '#facc15', // yellow-400 (Unmatched CM)
                        '#f87171', // red-400 (Unmatched COINS)
                    ],
                ],
            ],
            'labels' => ['Matched', 'Unmatched CM', 'Unmatched COINS'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
