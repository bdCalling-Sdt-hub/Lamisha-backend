<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Notification extends Model
{
    use HasFactory;

  protected $table = 'notifications';
  protected $keyType = 'string'; 
  public $incrementing = true;
  public $timestamps = true;

}
