<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Make sure to import Auth
use Illuminate\Http\Request; // Make sure to import Request

class ClientDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'resume',
        'license_certification',
        'libability_insurnce',
        'buisness_formations_doc',
        'enform',
        'currrent_driver_license',
        'current_cpr_certification',
        'blood_bron_pathogen_certificaton',
        'training_hipaa_osha',
        'management_service_aggriment',
        'nda',
        'deligation_aggriment',
        'ach_fomr',
        'appoinment_date',
        'appoinment_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
