<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $ok = Company::where('id', $request->route('company')->id)
            ->whereHas('users', function ($q) {
                $q->where('company_users.role', User::ROLE_ADMIN)
                    ->where('users.id', Auth::id());
            })
            ->exists();

        if (!$ok) {
            return response([
                'message' => 'Unauthorized!',
            ], 401);
        }

        return $next($request);
    }
}
