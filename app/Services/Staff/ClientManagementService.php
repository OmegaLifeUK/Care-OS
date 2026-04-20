<?php

namespace App\Services\Staff;

use App\Models\medicationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientManagementService
{

    public function store(array $data, int $homeId): medicationLog
    {
        DB::beginTransaction();
        try {
            $data['administrator_date'] = Carbon::parse($data['administrator_date'])->format('Y-m-d H:i:s');

            if (!empty($data['id'])) {
                $existing = medicationLog::active()->forHome($homeId)->find($data['id']);
                if (!$existing) {
                    throw new \Exception('Medication log not found or access denied.');
                }
                $existing->update($data);
                $mediLog = $existing;
            } else {
                $data['home_id'] = $homeId;
                $mediLog = medicationLog::create($data);
            }

            DB::commit();
            return $mediLog;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function list(int $homeId, ?int $clientId = null)
    {
        $query = medicationLog::active()->forHome($homeId);

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        return $query->latest('id')->paginate(10);
    }

    public function report_details(int $id, int $homeId)
    {
        return medicationLog::active()->forHome($homeId)->find($id);
    }

    public function delete(int $id, int $homeId): bool
    {
        $record = medicationLog::active()->forHome($homeId)->find($id);
        if (!$record) {
            return false;
        }
        $record->update(['is_deleted' => 1]);
        return true;
    }
}
