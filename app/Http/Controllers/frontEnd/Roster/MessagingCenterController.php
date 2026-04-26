<?php

namespace App\Http\Controllers\frontEnd\Roster;

use App\Http\Controllers\Controller;
use App\Services\Portal\PortalMessageService;
use Illuminate\Http\Request;

class MessagingCenterController extends Controller
{
    public function index()
    {
        $homeId = explode(',', auth()->user()->home_id)[0];
        $messageService = app(PortalMessageService::class);
        $clientsWithMessages = $messageService->getClientsWithMessages((int) $homeId);

        return view('frontEnd.roster.messaging.messaging_center', [
            'clients_with_messages' => $clientsWithMessages,
            'home_id' => $homeId,
        ]);
    }

    public function getThread(Request $request)
    {
        $request->validate(['client_id' => 'required|integer']);

        $homeId = explode(',', auth()->user()->home_id)[0];
        $messageService = app(PortalMessageService::class);

        $client = \App\ServiceUser::where('id', $request->client_id)
            ->where('home_id', $homeId)
            ->first();

        if (!$client) {
            return response()->json(['status' => false, 'message' => 'Client not found'], 404);
        }

        $thread = $messageService->getThreadForClient((int) $homeId, (int) $request->client_id);

        $thread->where('sender_type', 'family')->where('is_read', false)->each(function ($msg) use ($homeId) {
            $messageService = app(PortalMessageService::class);
            $messageService->markAsReadByStaff($msg->id, (int) $homeId, auth()->user()->name);
        });

        return response()->json([
            'status' => true,
            'client' => ['id' => $client->id, 'name' => $client->name],
            'messages' => $thread,
        ]);
    }

    public function reply(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'message_content' => 'required|string|max:5000',
            'subject' => 'nullable|string|max:255',
            'priority' => 'nullable|in:low,normal,high',
        ]);

        $homeId = explode(',', auth()->user()->home_id)[0];
        $messageService = app(PortalMessageService::class);

        try {
            $message = $messageService->sendStaffReply(
                (int) $homeId,
                auth()->user()->id,
                auth()->user()->name,
                (int) $request->client_id,
                $request->only(['message_content', 'subject', 'priority'])
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 404);
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
}
