<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class OnlyCompanyAdmin
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
        $exists  = $company->users()->wherePivot('role', User::ROLE_ADMIN)->wherePivot('user_id', $request->user()->id)->exists();

        if (!$exists) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
