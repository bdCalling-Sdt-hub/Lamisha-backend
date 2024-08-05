<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateProfile extends Model
{
    use HasFactory;
    protected $fillable = [

        'first_name',
        'last_name', 
        'phone',           
        'buisness_name', 
        'buisness_address', 
        'phone',           
        'image', 
        'email',                
    ];
}
