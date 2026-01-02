<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CoinsData extends Model
{
    use LogsActivity;

    protected $table = 'coins_data';

    protected $fillable = [
        'cm',
        'order',
        'container',
        'seal',
        'size_20',
        'size_40',
        'no_po',
        'kereta',
        'atd',
        'customer',
        'stasiun_asal',
        'stasiun_tujuan',
        'gudang_asal',
        'gudang_tujuan',
        'jenis',
        'service',
        'payment',
        'so',
        'submit_so',
        'nominal_ppn',
        'sa_ppn',
        'loading_ppn',
        'unloading_ppn',
        't_orig_ppn',
        't_dest_ppn',
        'sa',
        'loading',
        'unloading',
        't_orig',
        't_dest',
        'nominal',
        'klaim',
        'dokumen_klaim',
        'alur',
        'dokumen',
        'berat',
        'isi_barang',
        'ppcw',
        'owner',
        'wilayah_id',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'atd' => 'date',
            'submit_so' => 'date',
            'nominal_ppn' => 'integer',
            'sa_ppn' => 'integer',
            'loading_ppn' => 'integer',
            'unloading_ppn' => 'integer',
            't_orig_ppn' => 'integer',
            't_dest_ppn' => 'integer',
            'sa' => 'integer',
            'loading' => 'integer',
            'unloading' => 'integer',
            't_orig' => 'integer',
            't_dest' => 'integer',
            'nominal' => 'integer',
            'klaim' => 'integer',
            'berat' => 'integer',
        ];
    }

    /**
     * Get the wilayah this COINS data belongs to.
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    /**
     * Get the user who imported this data.
     */
    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    /**
     * Scope to find matching CM data.
     */
    public function scopeWithMatchingCm($query)
    {
        return $query->whereExists(function ($subquery) {
            $subquery->select(\DB::raw(1))
                ->from('cm_data')
                ->whereColumn('cm_data.cm', 'coins_data.cm')
                ->whereColumn('cm_data.container', 'coins_data.container');
        });
    }

    /**
     * Scope to find unmatched COINS data (no corresponding CM data).
     */
    public function scopeUnmatched($query)
    {
        return $query->whereNotExists(function ($subquery) {
            $subquery->select(\DB::raw(1))
                ->from('cm_data')
                ->whereColumn('cm_data.cm', 'coins_data.cm')
                ->whereColumn('cm_data.container', 'coins_data.container');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
