<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;


    protected $fillable = [
        'tier_id',
        'price_1',
        'price_2',
        // 'pricing_type',
        'duration',
        'service',

    ];

    public function tiear()
    {
        return $this->hasMany(Tier::class);
    }

}
