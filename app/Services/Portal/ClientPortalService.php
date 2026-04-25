<?php

namespace App\Services\Portal;

use App\Models\ClientPortalAccess;
use App\ServiceUser;
use Illuminate\Support\Facades\Log;

class ClientPortalService
{
    public function getDashboardData(ClientPortalAccess $access): array
    {
        $client = ServiceUser::where('id', $access->client_id)
            ->where('home_id', $access->home_id)
            ->first();

        return [
            'portal_access' => $access,
            'client' => $client,
            'stats' => [
                'upcoming_schedule' => 0,
                'unread_messages' => 0,
                'pending_requests' => 0,
                'notifications' => 0,
            ],
        ];
    }

    public function listPortalUsers(int $homeId, ?int $clientId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = ClientPortalAccess::active()
            ->forHome($homeId)
            ->with('client');

        if ($clientId) {
            $query->forClient($clientId);
        }

        return $query->orderBy('full_name')->get();
    }

    public function createPortalAccess(array $data, int $homeId, int $createdBy): ClientPortalAccess
    {
        $access = ClientPortalAccess::create(array_merge($data, [
            'home_id' => $homeId,
            'created_by' => $createdBy,
            'is_active' => 1,
            'is_deleted' => 0,
            'activation_date' => now()->toDateString(),
        ]));

        Log::info('Portal access created', [
            'id' => $access->id,
            'client_id' => $access->client_id,
            'user_email' => $access->user_email,
            'home_id' => $homeId,
            'created_by' => $createdBy,
        ]);

        return $access;
    }

    public function revokePortalAccess(int $id, int $homeId): bool
    {
        $access = ClientPortalAccess::where('id', $id)
            ->forHome($homeId)
            ->where('is_deleted', 0)
            ->first();

        if (!$access) {
            return false;
        }

        $access->update(['is_active' => 0]);

        Log::info('Portal access revoked', [
            'id' => $id,
            'home_id' => $homeId,
        ]);

        return true;
    }

    public function deletePortalAccess(int $id, int $homeId): bool
    {
        $access = ClientPortalAccess::where('id', $id)
            ->forHome($homeId)
            ->first();

        if (!$access) {
            return false;
        }

        $access->update(['is_deleted' => 1, 'is_active' => 0]);

        Log::info('Portal access deleted', [
            'id' => $id,
            'home_id' => $homeId,
        ]);

        return true;
    }
}
