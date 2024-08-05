<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    use HasFactory;
    
    protected $fillable = [

        'tyer_name',
        'protocols', 
        'standing_order',               
        'policies',
        'consents',
    ];
    public function price()
    {
        return $this->hasMany(Price::class);
    }
}
