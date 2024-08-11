<?php

namespace App\Http\Middleware\Api;

use App\Enums\TokenAbility;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->currentAccessToken()) {
            throw new AuthenticationException;
        }

        if (!$request->user()->tokenCan(TokenAbility::ISSUE_ACCESS_TOKEN->value)) {
            throw new AuthenticationException;
        }
        return $next($request);
    }
}
