<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientAlert extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['home_id','user_id','client_id','alert_type_id','severity','alert_title','description','action_required','expiry_date','requires_staff_acknowledgment','all','dashboard','care_plan','medication','visits','schedule','staff_acknowledgment_count','resolve_date','status','deleted_at'];
    
    public function alert_types(){
        return $this->belongsTo(AlertType::class, 'alert_type_id', 'id');
    }
}
