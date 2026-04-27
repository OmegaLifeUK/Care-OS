<?php

namespace App\Http\Controllers\frontEnd\Portal;

use App\Http\Controllers\Controller;
use App\Services\Portal\ClientPortalService;
use App\Services\Portal\PortalFeedbackService;
use App\Services\Portal\PortalMessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PortalDashboardController extends Controller
{
    protected $portalService;

    public function __construct(ClientPortalService $portalService)
    {
        $this->portalService = $portalService;
    }

    public function index(Request $request)
    {
        $portalAccess = $request->attributes->get('portal_access');
        $data = $this->portalService->getDashboardData($portalAccess);

        return view('frontEnd.portal.dashboard', $data);
    }

    public function schedule(Request $request)
    {
        $portalAccess = $request->attributes->get('portal_access');

        if (!$portalAccess->can_view_schedule) {
            return view('frontEnd.portal.schedule', [
                'portal_access' => $portalAccess,
                'access_denied' => true,
            ]);
        }

        $weekStart = $request->query('week');
        $data = $this->portalService->getScheduleData($portalAccess, $weekStart);

        return view('frontEnd.portal.schedule', array_merge($data, [
            'portal_access' => $portalAccess,
            'access_denied' => false,
        ]));
    }

    public function messages(Request $request)
    {
        $portalAccess = $request->attributes->get('portal_access');

        if (!$portalAccess->can_send_messages) {
            return view('frontEnd.portal.messages', [
                'portal_access' => $portalAccess,
                'access_denied' => true,
                'messages' => collect(),
                'stats' => ['total' => 0, 'unread' => 0, 'sent' => 0],
            ]);
        }

        $messageService = app(PortalMessageService::class);
        $messages = $messageService->getMessagesForPortal($portalAccess);
        $stats = [
            'total' => $messages->count(),
            'unread' => $messages->where('sender_type', 'staff')->where('is_read', false)->count(),
            'sent' => $messages->where('sender_type', 'family')->count(),
        ];

        return view('frontEnd.portal.messages', [
            'portal_access' => $portalAccess,
            'access_denied' => false,
            'messages' => $messages,
            'stats' => $stats,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message_content' => 'required|string|max:5000',
            'category' => 'required|in:general,schedule,medication,care_plan,feedback,concern,request',
            'priority' => 'required|in:low,normal,high',
            'replied_to_id' => 'nullable|integer',
        ]);

        $portalAccess = $request->attributes->get('portal_access');

        if (!$portalAccess->can_send_messages) {
            return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
        }

        $messageService = app(PortalMessageService::class);
        $message = $messageService->sendPortalMessage($portalAccess, $request->only([
            'subject', 'message_content', 'category', 'priority', 'replied_to_id',
        ]));

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function markMessageRead(Request $request, $id)
    {
        $portalAccess = $request->attributes->get('portal_access');
        $messageService = app(PortalMessageService::class);
        $result = $messageService->markAsRead((int) $id, $portalAccess);

        return response()->json(['status' => $result]);
    }

    public function feedback(Request $request)
    {
        $portalAccess = $request->attributes->get('portal_access');

        if (!$portalAccess->can_send_messages) {
            return view('frontEnd.portal.feedback', [
                'portal_access' => $portalAccess,
                'access_denied' => true,
                'feedback_list' => collect(),
                'stats' => ['total' => 0, 'with_responses' => 0, 'pending' => 0],
            ]);
        }

        $feedbackService = app(PortalFeedbackService::class);
        $feedbackList = $feedbackService->getFeedbackForPortal($portalAccess);
        $stats = $feedbackService->getFeedbackStats($portalAccess);

        return view('frontEnd.portal.feedback', [
            'portal_access' => $portalAccess,
            'access_denied' => false,
            'feedback_list' => $feedbackList,
            'stats' => $stats,
        ]);
    }

    public function submitFeedback(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'comments' => 'required|string|max:5000',
            'feedback_type' => 'required|in:compliment,complaint,suggestion,concern,general',
            'category' => 'required|in:staff_performance,care_quality,communication,punctuality,professionalism,facilities,safety,other',
            'rating' => 'required|integer|min:1|max:5',
            'relationship' => 'required|in:self,family,guardian,representative,other',
            'is_anonymous' => 'nullable|boolean',
            'wants_callback' => 'nullable|boolean',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        $portalAccess = $request->attributes->get('portal_access');

        if (!$portalAccess->can_send_messages) {
            return response()->json(['status' => false, 'message' => 'Permission denied'], 403);
        }

        $feedbackService = app(PortalFeedbackService::class);
        $feedback = $feedbackService->submitFeedback($portalAccess, $request->only([
            'subject', 'comments', 'feedback_type', 'category', 'rating',
            'relationship', 'is_anonymous', 'wants_callback', 'contact_email', 'contact_phone',
        ]));

        return response()->json(['status' => true, 'feedback' => $feedback]);
    }

    public function comingSoon(Request $request)
    {
        $portalAccess = $request->attributes->get('portal_access');
        $pageName = ucfirst(last(explode('/', $request->path())));

        return view('frontEnd.portal.coming_soon', [
            'portal_access' => $portalAccess,
            'page_name' => $pageName,
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget('portal_access_id');
        Session::forget('portal_client_id');

        $user = Auth::user();
        if ($user) {
            $user->update([
                'logged_in' => 0,
                'session_token' => '',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.');
    }
}
