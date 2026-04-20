<?php

namespace App\Services\Client;

use App\Models\Dol;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientDolsService
{

    public function store(array $data, int $homeId): Dol
    {
        DB::beginTransaction();
        try {
            if (!empty($data['id'])) {
                $existing = Dol::where('id', $data['id'])
                    ->where('home_id', $homeId)
                    ->first();
                if (!$existing) {
                    throw new \Exception('Record not found or access denied');
                }
            }

            $data['home_id'] = $homeId;
            $clientDols = Dol::updateOrCreate(['id' => $data['id'] ?? null], $data);
            DB::commit();
            return $clientDols;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function list(array $filters = [])
    {
        $query = Dol::query();

        if (!empty($filters['home_id'])) {
            $query->where('home_id', $filters['home_id']);
        }
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $dols = $query
            ->orderBy('id', 'desc')
            ->paginate(10);
        return $dols;
    }

    public function details($id, $homeId)
    {
        return Dol::where('id', $id)->where('home_id', $homeId)->first();
    }

    public function delete($id, $homeId)
    {
        DB::beginTransaction();
        try {
            $record = Dol::where('id', $id)->where('home_id', $homeId)->first();
            if (!$record) {
                throw new \Exception('Record not found or access denied');
            }
            $record->delete();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
