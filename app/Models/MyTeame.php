<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyTeame extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_nam',
        'last_name',
        'dob',
        'email',
        'phone',
        'Role',
        'license_certificate_number',
        'addisional_certificate',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
