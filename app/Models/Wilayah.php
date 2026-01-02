<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilayah extends Model
{
    protected $table = 'wilayahs';

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get all areas in this wilayah.
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Get all users assigned to this wilayah.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all COINS data imported for this wilayah.
     */
    public function coinsData(): HasMany
    {
        return $this->hasMany(CoinsData::class);
    }
}
