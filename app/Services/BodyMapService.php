<?php

namespace App\Services;

use App\Models\BodyMap;
use Illuminate\Support\Facades\Auth;

class BodyMapService
{
    /**
     * Get all active body map entries for a service user within a home.
     */
    public function listForServiceUser(int $homeId, int $serviceUserId): array
    {
        return BodyMap::forHome($homeId)
            ->active()
            ->where('service_user_id', $serviceUserId)
            ->select('id', 'sel_body_map_id', 'service_user_id', 'staff_id', 'su_risk_id',
                     'injury_type', 'injury_description', 'injury_date', 'injury_size', 'injury_colour')
            ->get()
            ->toArray();
    }

    /**
     * Get all active body map entries for a specific risk assessment.
     */
    public function listForRisk(int $homeId, int $suRiskId): array
    {
        return BodyMap::forHome($homeId)
            ->active()
            ->where('su_risk_id', $suRiskId)
            ->select('id', 'sel_body_map_id', 'service_user_id', 'staff_id', 'su_risk_id',
                     'injury_type', 'injury_description', 'injury_date', 'injury_size', 'injury_colour')
            ->get()
            ->toArray();
    }

    /**
     * Add a new injury point to the body map.
     */
    public function addInjury(int $homeId, array $data): BodyMap
    {
        return BodyMap::create([
            'home_id'            => $homeId,
            'service_user_id'    => $data['service_user_id'],
            'staff_id'           => Auth::id(),
            'su_risk_id'         => $data['su_risk_id'],
            'sel_body_map_id'    => $data['sel_body_map_id'],
            'injury_type'        => $data['injury_type'] ?? null,
            'injury_description' => $data['injury_description'] ?? null,
            'injury_date'        => $data['injury_date'] ?? now()->toDateString(),
            'injury_size'        => $data['injury_size'] ?? null,
            'injury_colour'      => $data['injury_colour'] ?? null,
            'is_deleted'         => '0',
            'created_by'         => Auth::id(),
        ]);
    }

    /**
     * Soft-delete an injury point (set is_deleted = 1).
     */
    public function removeInjury(int $homeId, int $id): bool
    {
        $injury = BodyMap::forHome($homeId)->active()->find($id);

        if (!$injury) {
            return false;
        }

        $injury->update([
            'is_deleted'  => '1',
            'updated_by'  => Auth::id(),
        ]);

        return true;
    }

    /**
     * Update injury details (type, description, size, colour, date).
     */
    public function updateInjury(int $homeId, int $id, array $data): bool
    {
        $injury = BodyMap::forHome($homeId)->active()->find($id);

        if (!$injury) {
            return false;
        }

        $injury->update([
            'injury_type'        => $data['injury_type'] ?? $injury->injury_type,
            'injury_description' => $data['injury_description'] ?? $injury->injury_description,
            'injury_date'        => $data['injury_date'] ?? $injury->injury_date,
            'injury_size'        => $data['injury_size'] ?? $injury->injury_size,
            'injury_colour'      => $data['injury_colour'] ?? $injury->injury_colour,
            'updated_by'         => Auth::id(),
        ]);

        return true;
    }

    /**
     * Get injury detail by ID.
     */
    public function getInjury(int $homeId, int $id): ?BodyMap
    {
        return BodyMap::forHome($homeId)
            ->active()
            ->with('staff:id,name')
            ->find($id);
    }

    /**
     * Get full injury history for a service user (including deleted/resolved).
     */
    public function getHistory(int $homeId, int $serviceUserId): array
    {
        return BodyMap::forHome($homeId)
            ->where('service_user_id', $serviceUserId)
            ->with('staff:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
