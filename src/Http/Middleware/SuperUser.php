<?php

namespace Aweram\UserManagement\Http\Middleware;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Closure;

class SuperUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|Redirector|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! $request->user())
            return redirect("login");
        if (! Gate::allows("super-user"))
            abort(403, __("Unauthorized action"));
        return $next($request);
    }
}
