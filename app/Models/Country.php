<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCountry
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'country_id'];

    protected $hidden = ['updated_at', 'created_at'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
