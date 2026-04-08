<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class medicationLog extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['home_id','user_id','client_id','medication_name','dosage','frequesncy','administrator_date','witnessed_by','notes','side_effect','status','deleted_at'];
}
