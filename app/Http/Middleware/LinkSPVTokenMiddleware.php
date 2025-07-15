<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LinkSPVTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Token');
        $expectedToken = env('LINK_SPV_TOKEN');

        if (empty($token) || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Invalid or missing token.'
            ], 401);
        }

        return $next($request);
    }
}
