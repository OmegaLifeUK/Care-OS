<?php
namespace App\Http\Controllers\Api\Staff;
use App\Http\Controllers\frontEnd\StaffManagementController;
use Illuminate\Http\Request;
use App\Models\StaffTraining;
use App\User;

class TrainingController extends StaffManagementController
{

    public function index($staff_member_id = null) 
    {
        $trainings['completed']     = StaffTraining::select('staff_training.id','t.training_name','t.training_provider',
                                    't.training_month','t.training_year','t.training_desc','staff_training.status')
                                        ->join('training as t', 't.id', '=', 'staff_training.training_id')
                                        ->where('staff_training.user_id',$staff_member_id)
                                        ->where('staff_training.status','=','1')
                                        ->get()
                                        ->toArray();

        $trainings['active']     = StaffTraining::select('staff_training.id','t.training_name','t.training_provider',
                                    't.training_month','t.training_year','t.training_desc','staff_training.status')
                                        ->join('training as t', 't.id', '=', 'staff_training.training_id')
                                        ->where('staff_training.user_id',$staff_member_id)
                                        ->where('staff_training.status','=','2')
                                        ->get()
                                        ->toArray();

        $trainings['not_completed']     = StaffTraining::select('staff_training.id','t.training_name','t.training_provider',
                                    't.training_month','t.training_year','t.training_desc','staff_training.status')
                                        ->join('training as t', 't.id', '=', 'staff_training.training_id')
                                        ->where('staff_training.user_id',$staff_member_id)
                                        ->where('staff_training.status','=','0')
                                        ->get()
                                        ->toArray();

        if( (empty($trainings['completed'])) && (empty($trainings['active'])) && (empty($trainings['not_completed'])) )
        {
            return json_encode(array(
                'result' => array(
                    'response' => false,
                    'message' => 'No trainings found.'
                )
            ));
        } else {
            return json_encode(array(
                'result' => array(
                    'response' => true,
                    'message' => "training list.",
                    'data' => $trainings
                )
            ));
        }
    }

    

}
