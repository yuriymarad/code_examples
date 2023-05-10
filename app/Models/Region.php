<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRegion
 */
class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'country_id'];

    protected $hidden = ['updated_at', 'created_at'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
