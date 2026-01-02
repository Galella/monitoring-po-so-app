<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CmData extends Model
{
    use LogsActivity;
    
    protected $table = 'cm_data';

    protected $fillable = [
        'ppcw',
        'container',
        'seal',
        'shipper',
        'consignee',
        'status',
        'commodity',
        'size',
        'weight',
        'keterangan',
        'cm',
        'atd',
        'no_order_coins',
        'area_id',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'atd' => 'date',
            'weight' => 'integer',
        ];
    }

    /**
     * Get the area this CM data belongs to.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the user who imported this data.
     */
    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    /**
     * Scope to find matching COINS data.
     */
    public function scopeWithMatchingCoins($query)
    {
        return $query->whereExists(function ($subquery) {
            $subquery->select(\DB::raw(1))
                ->from('coins_data')
                ->whereColumn('coins_data.cm', 'cm_data.cm')
                ->whereColumn('coins_data.container', 'cm_data.container');
        });
    }

    /**
     * Scope to find unmatched CM data (no corresponding COINS data).
     */
    public function scopeUnmatched($query)
    {
        return $query->whereNotExists(function ($subquery) {
            $subquery->select(\DB::raw(1))
                ->from('coins_data')
                ->whereColumn('coins_data.cm', 'cm_data.cm')
                ->whereColumn('coins_data.container', 'cm_data.container');
        });
    }

    /**
     * Get the matching COINS data.
     */
    public function matchingCoins()
    {
        return $this->hasOne(CoinsData::class, 'cm', 'cm')
            ->where('container', $this->container);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
