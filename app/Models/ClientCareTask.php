<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ServiceUser;
use App\User;
class ClientCareTask extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['home_id','user_id','task_title','task_type_id','task_category_id','priority','client_id','care_plan_id','task_tag','frequency','location','scheduled_date','scheduled_time','duration','carer_id','visit_id','shift_id','risk_level_id','safeguarding','two_person','ppe_required','risk_notes','task_description','status','deleted_at'];

    public function clientTaskType(){
        return $this->belongsTo(clientTaskType::class, 'task_type_id', 'id');
    }
    public function clientTaskCategorys(){
        return $this->belongsTo(clientTaskCategory::class,'task_category_id','id');
    }
    public function carers()
    {
        return $this->belongsTo(User::class, 'carer_id', 'id');
    }
    public function clients()
    {
        return $this->belongsTo(ServiceUser::class, 'client_id', 'id');
    }
    public function schedulesShift()
    {
        return $this->belongsTo(ScheduledShift::class, 'shif_id', 'id');
    }
}
