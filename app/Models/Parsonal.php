<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Parsonal extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'first_nam',
        'last_name',
        'dob',
        'email',
        'phone',
        'occupation',
        'state_license_certificate',
        'license_certificate_no',
        'completed_training_certificate_service',
        'mailing_address',
        
    ];
    public function buisness()
    {
        return $this->hasMany(BuisnessInfo::class);
    }
    public function appoinment()
    {
        return $this->hasMany(Appoinment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
