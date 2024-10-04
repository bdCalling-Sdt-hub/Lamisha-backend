<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Parsonal extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'first_name',
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


    //  Remove relations //
    public function removeBuisness(){
        return $this->hasOne(BuisnessInfo::class);
    }
    public function removeAppoinment(){
        return $this->hasOne(Appoinment::class);
    }
    public function getStateLicenseCertificateAttribute($value) {
        return json_decode($value, true);
    }
}
