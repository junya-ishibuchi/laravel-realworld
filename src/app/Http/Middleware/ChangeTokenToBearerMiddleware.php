<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeTokenToBearerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Authorization')) {
            $token = str_replace('Token ', '', $request->header('Authorization'));
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
