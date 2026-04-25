<?php

namespace App\Http\Controllers\frontEnd\Portal;

use App\Http\Controllers\Controller;
use App\Services\Portal\ClientPortalService;
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
