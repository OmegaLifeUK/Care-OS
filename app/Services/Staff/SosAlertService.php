<?php

namespace App\Services\Staff;

use App\Models\staffManagement\sosAlert;
use App\Notification;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SosAlertService
{
    public function trigger(int $staffId, int $homeId, ?string $message): sosAlert
    {
        DB::beginTransaction();
        try {
            $alert = sosAlert::create([
                'staff_id' => $staffId,
                'home_id' => $homeId,
                'location' => 'Web Dashboard',
                'message' => $message,
                'status' => 1,
            ]);

            $staffName = User::where('id', $staffId)->value('name') ?? 'Unknown';

            $managers = User::whereIn('user_type', ['M', 'A'])
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->whereRaw('FIND_IN_SET(?, home_id)', [$homeId])
                ->get();

            foreach ($managers as $manager) {
                $notification = new Notification;
                $notification->home_id = $homeId;
                $notification->user_id = $manager->id;
                $notification->event_id = $alert->id;
                $notification->notification_event_type_id = 24;
                $notification->event_action = 'SOS_ALERT';
                $notification->message = $staffName . ' needs help!';
                $notification->is_sticky = 1;
                $notification->save();
            }

            DB::commit();
            Log::info('SOS Alert triggered', ['alert_id' => $alert->id, 'staff_id' => $staffId, 'home_id' => $homeId]);
            return $alert;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function list(int $homeId, int $limit = 10)
    {
        return sosAlert::with(['staff:id,name', 'acknowledgedByUser:id,name', 'resolvedByUser:id,name'])
            ->forHome($homeId)
            ->active()
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    public function acknowledge(int $id, int $homeId, int $userId): ?sosAlert
    {
        $alert = sosAlert::forHome($homeId)->active()->where('id', $id)->first();
        if (!$alert) {
            return null;
        }
        if ($alert->status !== 1) {
            return null;
        }

        $alert->status = 2;
        $alert->acknowledged_by = $userId;
        $alert->acknowledged_at = now();
        $alert->save();

        Log::info('SOS Alert acknowledged', ['alert_id' => $id, 'user_id' => $userId, 'home_id' => $homeId]);
        return $alert;
    }

    public function resolve(int $id, int $homeId, int $userId, ?string $notes = null): ?sosAlert
    {
        $alert = sosAlert::forHome($homeId)->active()->where('id', $id)->first();
        if (!$alert) {
            return null;
        }
        if (!in_array($alert->status, [1, 2])) {
            return null;
        }

        $alert->status = 3;
        $alert->resolved_by = $userId;
        $alert->resolved_at = now();
        if ($notes) {
            $alert->message = ($alert->message ? $alert->message . "\n\nResolution: " : 'Resolution: ') . $notes;
        }
        $alert->save();

        Log::info('SOS Alert resolved', ['alert_id' => $id, 'user_id' => $userId, 'home_id' => $homeId]);
        return $alert;
    }
}
