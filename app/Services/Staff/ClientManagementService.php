<?php

namespace App\Services\Staff;

use App\Models\medicationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientManagementService
{
    
    public function store(array $data): medicationLog
    {
        DB::beginTransaction();
        try{
            $data['administrator_date'] = Carbon::parse($data['administrator_date'])->format('Y-m-d H:i:s');
            $mediLog = medicationLog::updateOrCreate(['id' => $data['id'] ?? null],$data);
            DB::commit();
            return $mediLog;
        }catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error saving Client Medication log:', [
            //     'error' => $e->getMessage(),
            //     'data'  => $data
            // ]);
            throw $e;
        }
        
    }

    
    public function list(array $filters = [])
    {
        $query = medicationLog::query();
        if (!empty($filters['home_id'])) {
            $query->where('home_id', $filters['home_id']);
        }
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        return $query->latest('id')->paginate(10);
    }
    public function report_details($id){
        return medicationLog::find($id);
    }
}
