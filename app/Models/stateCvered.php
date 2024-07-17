<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stateCvered extends Model
{
    use HasFactory;
    protected $fillable = [    
      'state_name'
    ];
}
