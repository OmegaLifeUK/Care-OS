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

    /**
     * #2: Check if current user is an admin (type=A).
     */
    private function isAdmin(): bool
    {
        return Auth::user()->user_type === 'A';
    }

    public function index()
    {
        $year = request('year', date('Y'));
        $home_id = $this->getHomeId();
        $trainings = $this->trainingService->list($home_id, $year);

        return View::make('frontEnd.staffManagement.training_listing')
            ->with('training', !empty($trainings) ? $trainings : '')
            ->with('is_admin', $this->isAdmin());
    }

    public function add(Request $request)
    {
        // #2: Only admins can create trainings
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can add trainings.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'training_provider' => 'required|string|max:255',
            'desc' => 'required|string|max:5000',
            'training_date' => 'required|date|after_or_equal:today',
            'is_mandatory' => 'nullable|boolean',
            'category' => 'nullable|string|in:mandatory,recommended,optional',
            'expiry_months' => 'nullable|integer|min:1|max:120',
            'max_employees' => 'nullable|integer|min:1',
        ]);

        $home_id = $this->getHomeId();
        $training = $this->trainingService->create($home_id, $request->input());

        if ($training) {
            return redirect()->back()->with('success', 'Training "' . e($training->training_name) . '" added successfully.');
        }
        return redirect()->back()->with('error', 'Failed to create training. Please try again.');
    }

    public function view($id = null)
    {
        $home_id = $this->getHomeId();
        $detail = $this->trainingService->getDetail($home_id, $id);

        if (!$detail) {
            return redirect('/staff/trainings')->with('error', 'Training not found or you do not have access.');
        }

        $home_users = User::select('name', 'id')
            ->where('home_id', $home_id)
            ->where('is_deleted', '0')
            ->get()
            ->toArray();

        // #5: Get remaining capacity for display
        $remainingCapacity = $this->trainingService->getRemainingCapacity($id);

        return view('frontEnd.staffManagement.training_view', [
            'completed_training' => $detail['completed'],
            'active_training' => $detail['active'],
            'not_completed_training' => $detail['not_completed'],
            'training_id' => $id,
            'training_name' => $detail['training']->training_name,
            'training' => $detail['training'],
            'home_users' => $home_users,
            'is_admin' => $this->isAdmin(),
            'remaining_capacity' => $remainingCapacity,
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
        // #2: Only admins can change training status
        if (!$this->isAdmin()) {
            return Redirect::back()->with('error', 'Only administrators can update training status.');
        }

        $status = $request->input('status');
        if (!$training_id || !$status) {
            return Redirect::back()->with('error', 'Invalid request. Training ID and status are required.');
        }

        $home_id = $this->getHomeId();
        $updated = $this->trainingService->updateStaffStatus($home_id, $training_id, $status);

        if ($updated) {
            return Redirect::back()->with('success', 'Training status updated successfully.');
        }
        return Redirect::back()->with('error', 'Could not update status. The record may not exist or you do not have access.');
    }

    public function add_user_training(Request $request)
    {
        // #2: Only admins can assign staff
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can assign staff to trainings.');
        }

        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:user,id',
            'training_id' => 'required|integer|exists:training,id',
        ]);

        $home_id = $this->getHomeId();
        $result = $this->trainingService->assignStaff($home_id, $request->training_id, $request->user_ids);

        // #5: Handle capacity full response
        if ($result === 'full') {
            return redirect()->back()->with('error', 'This training has reached its maximum number of employees. No more staff can be assigned.');
        }

        if ($result > 0) {
            // #3: Send email asynchronously via queue
            $training = Training::forHome($home_id)->where('id', $request->training_id)->first();
            $userIds = $request->user_ids;
            $lastUserId = end($userIds);
            $trainee = User::select('name', 'id', 'email')
                ->where('home_id', $home_id)
                ->where('id', $lastUserId)
                ->where('is_deleted', '0')
                ->first();

            if ($trainee && $training && filter_var($trainee->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $emailData = [
                        'name' => $trainee->name,
                        'training_name' => $training->training_name,
                        'training_provider' => $training->training_provider,
                        'training_month' => $training->training_month,
                        'training_year' => $training->training_year,
                        'training_desc' => $training->training_desc,
                    ];
                    $traineeEmail = $trainee->email;

                    // #3: Queue the email so it doesn't block the request
                    Mail::queue('emails.selected_trainee', $emailData, function ($message) use ($traineeEmail) {
                        $message->to($traineeEmail)->subject('Care OS Training Assignment');
                    });
                } catch (\Exception $e) {
                    // Email failure shouldn't block the assignment
                }
            }

            // #5: Include remaining capacity in success message
            $remaining = $this->trainingService->getRemainingCapacity($request->training_id);
            $capacityMsg = $remaining !== null ? " ({$remaining} slot(s) remaining)" : '';
            return redirect()->back()->with('success', "Training assigned to {$result} staff member(s).{$capacityMsg}");
        }
        return redirect()->back()->with('error', 'Staff already assigned or no valid staff members found.');
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
            'training_date' => $training->training_date ? $training->training_date->format('Y-m-d') : null,
            'training_month' => $training->training_month,
            'training_year' => $training->training_year,
            'training_desc' => $training->training_desc,
            'is_mandatory' => $training->is_mandatory,
            'category' => $training->category,
            'expiry_months' => $training->expiry_months,
            'max_employees' => $training->max_employees,
        ]);
    }

    public function edit_fields(Request $request)
    {
        // #2: Only admins can edit trainings
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can edit trainings.');
        }

        $request->validate([
            'training_id' => 'required|integer|exists:training,id',
            'name' => 'required|string|max:255',
            'training_provider' => 'required|string|max:255',
            'desc' => 'required|string|max:5000',
            'training_date' => 'required|date',
            'is_mandatory' => 'nullable|boolean',
            'category' => 'nullable|string|in:mandatory,recommended,optional',
            'expiry_months' => 'nullable|integer|min:1|max:120',
            'max_employees' => 'nullable|integer|min:1',
        ]);

        $home_id = $this->getHomeId();
        $updated = $this->trainingService->update($home_id, $request->training_id, $request->input());

        if ($updated) {
            return redirect()->back()->with('success', 'Training updated successfully.');
        }
        return redirect()->back()->with('error', 'Failed to update training. It may not exist or you do not have access.');
    }

    public function delete(Request $request, $training_id)
    {
        // #2: Only admins can delete trainings
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Only administrators can delete trainings.');
        }

        $home_id = $this->getHomeId();
        $deleted = $this->trainingService->delete($home_id, $training_id);

        if ($deleted) {
            return redirect('/staff/trainings')->with('success', 'Training record deleted successfully.');
        }
        return redirect()->back()->with('error', 'Failed to delete training. It may not exist or you do not have access.');
    }
}
