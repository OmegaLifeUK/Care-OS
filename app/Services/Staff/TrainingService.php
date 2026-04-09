<?php

namespace App\Services\Staff;

use App\Models\Training;
use App\Models\StaffTraining;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrainingService
{
    /**
     * List trainings grouped by month for a given home and year.
     */
    public function list(int $homeId, string $year): array
    {
        $trainings = Training::forHome($homeId)
            ->active()
            ->where('training_year', $year)
            ->orderBy('training_month', 'asc')
            ->get();

        $grouped = [];
        foreach ($trainings as $training) {
            $grouped[$training->training_month][] = [
                'id' => $training->id,
                'name' => $training->training_name,
                'is_mandatory' => $training->is_mandatory,
            ];
        }

        return $grouped;
    }

    /**
     * Create a new training module.
     */
    public function create(int $homeId, array $data): Training
    {
        return Training::create([
            'home_id' => $homeId,
            'training_name' => $data['name'],
            'training_provider' => $data['training_provider'],
            'training_desc' => $data['desc'],
            'training_month' => $data['month'],
            'training_year' => $data['year'],
            'is_mandatory' => $data['is_mandatory'] ?? 0,
            'category' => $data['category'] ?? null,
            'expiry_months' => $data['expiry_months'] ?? null,
            'status' => 0,
        ]);
    }

    /**
     * Update an existing training module (home-scoped).
     */
    public function update(int $homeId, int $trainingId, array $data): bool
    {
        $training = Training::forHome($homeId)->active()->where('id', $trainingId)->first();
        if (!$training) {
            return false;
        }

        $training->training_name = $data['name'];
        $training->training_provider = $data['training_provider'];
        $training->training_desc = $data['desc'];
        $training->training_month = $data['month'];
        $training->training_year = $data['year'];
        $training->is_mandatory = $data['is_mandatory'] ?? $training->is_mandatory;
        $training->category = $data['category'] ?? $training->category;
        $training->expiry_months = $data['expiry_months'] ?? $training->expiry_months;

        return $training->save();
    }

    /**
     * Soft-delete a training module (home-scoped).
     */
    public function delete(int $homeId, int $trainingId): bool
    {
        return Training::forHome($homeId)
            ->where('id', $trainingId)
            ->update(['is_deleted' => 1]) > 0;
    }

    /**
     * Get training detail with staff breakdown (home-scoped).
     */
    public function getDetail(int $homeId, int $trainingId): ?array
    {
        $training = Training::forHome($homeId)->active()->where('id', $trainingId)->first();
        if (!$training) {
            return null;
        }

        $baseQuery = fn($status) => DB::table('user')
            ->join('staff_training', 'staff_training.user_id', '=', 'user.id')
            ->where('staff_training.training_id', $trainingId)
            ->where('user.home_id', $homeId)
            ->where('staff_training.status', $status)
            ->select('user.name', 'staff_training.id', 'staff_training.completed_date', 'staff_training.expiry_date');

        return [
            'training' => $training,
            'completed' => $baseQuery(StaffTraining::STATUS_COMPLETED)->paginate(7),
            'active' => $baseQuery(StaffTraining::STATUS_ACTIVE)->paginate(5),
            'not_completed' => $baseQuery(StaffTraining::STATUS_NOT_STARTED)->paginate(5),
        ];
    }

    /**
     * Get paginated staff for a specific status (for AJAX pagination).
     */
    public function getStaffByStatus(int $homeId, int $trainingId, int $status, int $perPage = 5)
    {
        return DB::table('user')
            ->join('staff_training', 'staff_training.user_id', '=', 'user.id')
            ->where('staff_training.training_id', $trainingId)
            ->where('user.home_id', $homeId)
            ->where('staff_training.status', $status)
            ->select('user.name', 'staff_training.id', 'staff_training.completed_date', 'staff_training.expiry_date')
            ->paginate($perPage);
    }

    /**
     * Assign staff members to a training (with duplicate check and expiry calc).
     */
    public function assignStaff(int $homeId, int $trainingId, array $userIds): int
    {
        $training = Training::forHome($homeId)->active()->where('id', $trainingId)->first();
        if (!$training) {
            return 0;
        }

        // Get already-assigned user IDs to prevent duplicates
        $existingUserIds = StaffTraining::where('training_id', $trainingId)
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')
            ->toArray();

        $newUserIds = array_diff($userIds, $existingUserIds);
        $count = 0;

        foreach ($newUserIds as $userId) {
            // Verify user belongs to this home
            $user = User::where('id', $userId)->where('home_id', $homeId)->where('is_deleted', '0')->first();
            if (!$user) {
                continue;
            }

            StaffTraining::create([
                'user_id' => $userId,
                'training_id' => $trainingId,
                'status' => StaffTraining::STATUS_ACTIVE,
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Update a staff training status (home-scoped via user).
     */
    public function updateStaffStatus(int $homeId, int $staffTrainingId, string $status): bool
    {
        // Join through user to verify home_id
        $staffTraining = StaffTraining::join('user', 'user.id', '=', 'staff_training.user_id')
            ->where('staff_training.id', $staffTrainingId)
            ->where('user.home_id', $homeId)
            ->select('staff_training.*')
            ->first();

        if (!$staffTraining) {
            return false;
        }

        $statusMap = [
            'complete' => StaffTraining::STATUS_COMPLETED,
            'activate' => StaffTraining::STATUS_ACTIVE,
            'notcompleted' => StaffTraining::STATUS_NOT_STARTED,
        ];

        if (!isset($statusMap[$status])) {
            return false;
        }

        $staffTraining->status = $statusMap[$status];

        // Set completed_date and calculate expiry when marking complete
        if ($status === 'complete') {
            $staffTraining->completed_date = Carbon::today();

            // Calculate expiry_date if the training has expiry_months
            $training = Training::find($staffTraining->training_id);
            if ($training && $training->expiry_months) {
                $staffTraining->expiry_date = Carbon::today()->addMonths($training->expiry_months);
            }
        }

        // Clear completed_date if un-completing
        if ($status === 'notcompleted' || $status === 'activate') {
            $staffTraining->completed_date = null;
            $staffTraining->expiry_date = null;
        }

        return $staffTraining->save();
    }

    /**
     * Get training fields for the edit modal (home-scoped).
     */
    public function getFields(int $homeId, int $trainingId): ?Training
    {
        return Training::forHome($homeId)->where('id', $trainingId)->first();
    }

    /**
     * Get trainings expiring within N days for a home.
     */
    public function getExpiringTrainings(int $homeId, int $days = 30)
    {
        return StaffTraining::with(['training', 'user'])
            ->join('user', 'user.id', '=', 'staff_training.user_id')
            ->join('training', 'training.id', '=', 'staff_training.training_id')
            ->where('user.home_id', $homeId)
            ->where('training.is_deleted', 0)
            ->where('staff_training.status', StaffTraining::STATUS_COMPLETED)
            ->where('staff_training.expiry_date', '<=', Carbon::today()->addDays($days))
            ->select('staff_training.*', 'user.name as staff_name', 'training.training_name')
            ->get();
    }
}
