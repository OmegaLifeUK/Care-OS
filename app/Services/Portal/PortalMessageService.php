<?php

namespace App\Services\Portal;

use App\Models\ClientPortalAccess;
use App\Models\ClientPortalMessage;
use App\ServiceUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PortalMessageService
{
    public function getMessagesForPortal(ClientPortalAccess $access): Collection
    {
        $messages = ClientPortalMessage::forClient($access->client_id)
            ->forHome($access->home_id)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        // GDPR: truncate staff sender_name to first name only
        $messages->each(function ($msg) {
            if ($msg->sender_type === 'staff') {
                $msg->sender_name = explode(' ', $msg->sender_name)[0];
            }
        });

        return $messages;
    }

    public function sendPortalMessage(ClientPortalAccess $access, array $data): ClientPortalMessage
    {
        $message = ClientPortalMessage::create([
            'home_id' => $access->home_id,
            'client_id' => $access->client_id,
            'sender_type' => 'family',
            'sender_id' => $access->id,
            'sender_name' => $access->full_name,
            'recipient_type' => 'all_staff',
            'subject' => $data['subject'],
            'message_content' => $data['message_content'],
            'category' => $data['category'],
            'priority' => $data['priority'],
            'replied_to_id' => $data['replied_to_id'] ?? null,
            'status' => 'sent',
            'is_read' => 0,
            'is_deleted' => 0,
            'created_by' => $access->id,
        ]);

        Log::info('Portal message sent', [
            'message_id' => $message->id,
            'client_id' => $access->client_id,
            'sender' => $access->full_name,
            'home_id' => $access->home_id,
        ]);

        return $message;
    }

    public function markAsRead(int $messageId, ClientPortalAccess $access): bool
    {
        $message = ClientPortalMessage::where('id', $messageId)
            ->forClient($access->client_id)
            ->forHome($access->home_id)
            ->where('sender_type', 'staff')
            ->active()
            ->first();

        if (!$message) {
            return false;
        }

        $message->update([
            'is_read' => 1,
            'read_at' => now(),
            'read_by' => $access->full_name,
            'status' => 'read',
        ]);

        return true;
    }

    public function getUnreadCount(ClientPortalAccess $access): int
    {
        return ClientPortalMessage::forClient($access->client_id)
            ->forHome($access->home_id)
            ->where('sender_type', 'staff')
            ->unread()
            ->active()
            ->count();
    }

    // --- Admin methods ---

    public function getClientsWithMessages(int $homeId): Collection
    {
        $messages = ClientPortalMessage::forHome($homeId)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $messages->groupBy('client_id');

        $clients = $grouped->map(function ($msgs, $clientId) {
            $client = ServiceUser::find($clientId);
            if (!$client) {
                return null;
            }

            $unread = $msgs->where('sender_type', 'family')->where('is_read', false)->count();
            $urgent = $msgs->where('sender_type', 'family')->where('is_read', false)->where('priority', 'high')->count();
            $lastMessage = $msgs->first();

            return (object) [
                'id' => $clientId,
                'name' => $client->name ?? 'Unknown',
                'last_message' => $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message_content, 50) : '',
                'last_message_date' => $lastMessage ? $lastMessage->created_at : null,
                'unread_count' => $unread,
                'urgent_count' => $urgent,
                'total_count' => $msgs->count(),
            ];
        })->filter()->sortByDesc(function ($c) {
            return [$c->urgent_count, $c->unread_count, $c->last_message_date];
        })->values();

        return $clients;
    }

    public function getThreadForClient(int $homeId, int $clientId): Collection
    {
        return ClientPortalMessage::forHome($homeId)
            ->forClient($clientId)
            ->active()
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendStaffReply(int $homeId, int $staffId, string $staffName, int $clientId, array $data): ClientPortalMessage
    {
        $client = ServiceUser::where('id', $clientId)
            ->where('home_id', $homeId)
            ->first();

        if (!$client) {
            throw new \InvalidArgumentException('Client not found in this home');
        }

        $message = ClientPortalMessage::create([
            'home_id' => $homeId,
            'client_id' => $clientId,
            'sender_type' => 'staff',
            'sender_id' => $staffId,
            'sender_name' => $staffName,
            'recipient_type' => 'family',
            'subject' => $data['subject'] ?? 'Re: Message',
            'message_content' => $data['message_content'],
            'priority' => $data['priority'] ?? 'normal',
            'category' => 'general',
            'status' => 'sent',
            'is_read' => 0,
            'is_deleted' => 0,
            'created_by' => $staffId,
        ]);

        Log::info('Staff reply sent', [
            'message_id' => $message->id,
            'client_id' => $clientId,
            'staff_id' => $staffId,
            'home_id' => $homeId,
        ]);

        return $message;
    }

    public function markAsReadByStaff(int $messageId, int $homeId, string $staffName): bool
    {
        $message = ClientPortalMessage::where('id', $messageId)
            ->forHome($homeId)
            ->where('sender_type', 'family')
            ->active()
            ->first();

        if (!$message) {
            return false;
        }

        $message->update([
            'is_read' => 1,
            'read_at' => now(),
            'read_by' => $staffName,
            'status' => 'read',
        ]);

        return true;
    }
}
