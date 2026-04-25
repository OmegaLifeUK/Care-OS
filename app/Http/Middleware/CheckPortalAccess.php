<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ClientPortalAccess;
use Illuminate\Support\Facades\Session;

class CheckPortalAccess
{
    public function handle(Request $request, Closure $next)
    {
        $portalAccessId = Session::get('portal_access_id');

        if (!$portalAccessId) {
            return redirect('/roster');
        }

        $portalAccess = ClientPortalAccess::active()
            ->where('id', $portalAccessId)
            ->first();

        if (!$portalAccess) {
            Session::forget('portal_access_id');
            Session::forget('portal_client_id');
            return redirect('/roster');
        }

        $request->attributes->set('portal_access', $portalAccess);

        return $next($request);
    }
}
