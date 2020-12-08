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
        '/api/*',
    ];

    /**
     * Consider that tokens match when we are in development env.
     * */
    protected function tokensMatch($request)
    {
        return parent::tokensMatch($request);
    }
}
