<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
        'code',
        'wilayah_id',
    ];

    /**
     * Get the wilayah that this area belongs to.
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    /**
     * Get all users assigned to this area.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all CM data imported for this area.
     */
    public function cmData(): HasMany
    {
        return $this->hasMany(CmData::class);
    }
}
