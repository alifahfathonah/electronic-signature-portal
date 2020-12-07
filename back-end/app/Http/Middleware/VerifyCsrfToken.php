<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Consider that tokens match when we are in development env.
     * */
    protected function tokensMatch($request)
    {
        if (config('app.env') !== 'development' && config('app.bypass-csrf')) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
