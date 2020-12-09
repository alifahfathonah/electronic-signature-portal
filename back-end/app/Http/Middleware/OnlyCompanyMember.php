<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OnlyCompanyMember
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $company = $request->route('company');
        $exists  = $company->users()->wherePivot('user_id', $request->user()->id)->exists();

        if (!$exists) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
