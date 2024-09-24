<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;
    protected $fillable =[
        "user_id",
        "vendor_ordering",
        "onoarding_fee",
        "ach_payment",
    ];
}
