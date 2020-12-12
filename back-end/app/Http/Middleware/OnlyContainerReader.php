<?php

namespace App\Http\Middleware;

use App\Models\SignatureContainer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlyContainerReader
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
        // TODO implement idcode based access restriction for signatures
        return $next($request);

        $containerId = $request->route('container_id');
        $container   = SignatureContainer::where('public_id', $containerId)->firstOrFail();

        if (Auth::check()) {
            $ok = $container->users()->where('user_id', Auth::id())->exists();
            if ($ok) {
                return $next($request);
            }
        }

        $msg = Auth::check()
            ? 'Cannot access container.'
            : 'Cannot access container. Please log in!';

        return response([
            'message' => $msg
        ], 401);
    }
}
