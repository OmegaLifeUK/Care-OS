<?php
namespace App\Http\Controllers\frontEnd\StaffManagement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\StaffTraining;
use App\User;
use App\Services\Staff\TrainingService;
use Auth, View, Redirect;
use Illuminate\Support\Facades\Mail;

class TrainingController extends Controller
{
    protected $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    private function getHomeId()
    {
        $home_ids = Auth::user()->home_id;
        $ex_home_ids = explode(',', $home_ids);
        return $ex_home_ids[0];
    }

    public function index()
    {
        $year = request('year', date('Y'));
        $home_id = $this->getHomeId();
        $trainings = $this->trainingService->list($home_id, $year);

        return View::make('frontEnd.staffManagement.training_listing')
            ->with('training', !empty($trainings) ? $trainings : '');
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'training_provider' => 'required|string|max:255',
            'desc' => 'required|string|max:5000',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|digits:4',
            'is_mandatory' => 'nullable|boolean',
            'category' => 'nullable|string|in:mandatory,recommended,optional',
            'expiry_months' => 'nullable|integer|min:1|max:120',
        ]);

        $home_id = $this->getHomeId();
        $training = $this->trainingService->create($home_id, $request->input());

        if ($training) {
            return redirect()->back()->with('success', 'Training added successfully.');
        }
        return redirect()->back()->with('error', COMMON_ERROR);
    }

    public function view($id = null)
    {
        $home_id = $this->getHomeId();
        $detail = $this->trainingService->getDetail($home_id, $id);

        if (!$detail) {
            return redirect('/staff/trainings')->with('error', 'Training not found.');
        }

        $home_users = User::select('name', 'id')
            ->where('home_id', $home_id)
            ->where('is_deleted', '0')
            ->get()
            ->toArray();

        return view('frontEnd.staffManagement.training_view', [
            'completed_training' => $detail['completed'],
            'active_training' => $detail['active'],
            'not_completed_training' => $detail['not_completed'],
            'training_id' => $id,
            'training_name' => $detail['training']->training_name,
            'training' => $detail['training'],
            'home_users' => $home_users,
        ]);
    }

    public function completed_training($id = null)
    {
        $home_id = $this->getHomeId();
        $completed_training = $this->trainingService->getStaffByStatus($home_id, $id, StaffTraining::STATUS_COMPLETED, 1);

        foreach ($completed_training as $complete) {
            echo '<div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <a href="">' . e($complete->name) . '</a> <span class="color-green m-l-15"><i class="fa fa-check"></i></span>
                    </div>';
        }

        echo '<div class="col-md-12 col-sm-12 col-xs-12 clearfix completed">' .
                $completed_training->links() .
            '</div>';
        die;
    }

    public function active_training($id = null)
    {
        $home_id = $this->getHomeId();
        $active_training = $this->trainingService->getStaffByStatus($home_id, $id, StaffTraining::STATUS_ACTIVE, 1);

        echo '<div class="form-group col-md-12 col-sm-12 col-xs-12 cog-panel p-0">';
        foreach ($active_training as $active) {
            echo '<div class="col-md-12 col-sm-12 col-xs-12 cog-panel p-0">
                    <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <a href="">' . e($active->name) . '</a>
                        <span class="m-l-15 clr-blue settings setting-sze">
                            <i class="fa fa-cog"></i>
                            <div class="pop-notifbox">
                                <ul class="pop-notification" type="none">
                                    <li> <a href="' . url('/staff/training/status/update') . '/' . $active->id . '?status=complete"> <span class="color-green"> <i class="fa fa-check"></i> </span> Mark complete </a> </li>
                                    <li> <a href="' . url('/staff/training/status/update') . '/' . $active->id . '?status=notcompleted"> <span class="color-red"> <i class="fa fa-exclamation-circle"></i> </span> Mark uncomplete </a> </li>
                                </ul>
                            </div>
                        </span>
                    </div>
                </div>';
        }
        echo '</div>
                <div class="col-md-12 col-sm-12 col-xs-12 clearfix active_training">' .
                    $active_training->links() .
                '</div>';
        die;
    }

    public function not_completed_training($id = null)
    {
        $home_id = $this->getHomeId();
        $not_completed_training = $this->trainingService->getStaffByStatus($home_id, $id, StaffTraining::STATUS_NOT_STARTED, 1);

        foreach ($not_completed_training as $not) {
            echo '<div class="form-group col-md-12 col-sm-12 col-xs-12">
                    <a href="">' . e($not->name) . '</a>
                    <span class="m-l-15 clr-blue settings setting-sze">
                        <i class="fa fa-cog"></i>
                        <div class="pop-notifbox">
                            <ul type="none" class="pop-notification">
                                <li> <a href="' . url('/staff/training/status/update') . '/' . $not->id . '?status=activate"> <span> <i class="fa fa-pencil"></i> </span> Mark Active </a> </li>
                                <li> <a href="' . url('/staff/training/status/update') . '/' . $not->id . '?status=complete"> <span class="color-green"> <i class="fa fa-check"></i> </span> Mark complete </a> </li>
                            </ul>
                        </div>
                    </span>
                </div>';
        }

        echo '<div class="col-md-12 col-sm-12 col-xs-12 clearfix not-completed">' .
                $not_completed_training->links() .
            '</div>';
        die;
    }

    public function status_update(Request $request, $training_id = null)
    {
        $status = $request->input('status');
        if (!$training_id || !$status) {
            return Redirect::back();
        }

        $home_id = $this->getHomeId();
        $updated = $this->trainingService->updateStaffStatus($home_id, $training_id, $status);

        if ($updated) {
            return Redirect::back()->with('success', 'Training status updated.');
        }
        return Redirect::back()->with('error', 'Could not update status.');
    }

    public function add_user_training(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:user,id',
            'training_id' => 'required|integer|exists:training,id',
        ]);

        $home_id = $this->getHomeId();
        $count = $this->trainingService->assignStaff($home_id, $request->training_id, $request->user_ids);

        if ($count > 0) {
            // Send email to the last assigned trainee
            $training = Training::forHome($home_id)->where('id', $request->training_id)->first();
            $lastUserId = end($request->user_ids);
            $trainee = User::select('name', 'id', 'email')
                ->where('home_id', $home_id)
                ->where('id', $lastUserId)
                ->where('is_deleted', '0')
                ->first();

            if ($trainee && $training && filter_var($trainee->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::send('emails.selected_trainee', [
                        'name' => $trainee->name,
                        'training_name' => $training->training_name,
                        'training_provider' => $training->training_provider,
                        'training_month' => $training->training_month,
                        'training_year' => $training->training_year,
                        'training_desc' => $training->training_desc,
                    ], function ($message) use ($trainee) {
                        $message->to($trainee->email)->subject('Care OS Training Assignment');
                    });
                } catch (\Exception $e) {
                    // Email failure shouldn't block the assignment
                }
            }

            return redirect()->back()->with('success', "Training assigned to {$count} staff member(s).");
        }
        return redirect()->back()->with('error', 'Staff already assigned or not found.');
    }

    public function view_fields(Request $request, $training_id)
    {
        $home_id = $this->getHomeId();
        $training = $this->trainingService->getFields($home_id, $training_id);

        if (!$training) {
            return response()->json(['response' => false]);
        }

        return response()->json([
            'response' => true,
            'training_id' => $training->id,
            'training_name' => $training->training_name,
            'training_provider' => $training->training_provider,
            'training_month' => $training->training_month,
            'training_year' => $training->training_year,
            'training_desc' => $training->training_desc,
            'is_mandatory' => $training->is_mandatory,
            'category' => $training->category,
            'expiry_months' => $training->expiry_months,
        ]);
    }

    public function edit_fields(Request $request)
    {
        $request->validate([
            'training_id' => 'required|integer|exists:training,id',
            'name' => 'required|string|max:255',
            'training_provider' => 'required|string|max:255',
            'desc' => 'required|string|max:5000',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|digits:4',
            'is_mandatory' => 'nullable|boolean',
            'category' => 'nullable|string|in:mandatory,recommended,optional',
            'expiry_months' => 'nullable|integer|min:1|max:120',
        ]);

        $home_id = $this->getHomeId();
        $updated = $this->trainingService->update($home_id, $request->training_id, $request->input());

        if ($updated) {
            return redirect()->back()->with('success', 'Staff Training updated successfully.');
        }
        return redirect()->back()->with('error', COMMON_ERROR);
    }

    public function delete(Request $request, $training_id)
    {
        $home_id = $this->getHomeId();
        $deleted = $this->trainingService->delete($home_id, $training_id);

        if ($deleted) {
            return redirect('/staff/trainings')->with('success', 'Training record deleted successfully.');
        }
        return redirect()->back()->with('error', COMMON_ERROR);
    }
}
