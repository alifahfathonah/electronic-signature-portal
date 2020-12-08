<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class OnlyCompanyAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $ok = Company::where('id', $request->route('company_id'))
            ->whereHas('users', function ($q) {
                $q->wherePivot('role', User::ROLE_ADMIN)
                    ->where('id', Auth::id());
            })
            ->exists();

        if (!$ok) {
            throw new UnauthorizedException;
        }
        return $next($request);
    }
}
