<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Token; // Add this import

class ApiTokensMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Token');

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'The token expired.'
            ], 401);
        }

        $apiToken = Token::where('token', hash('sha256', $token))->first();
        if($apiToken->used >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'The token expired.'
            ], 401);
        }

        if ($apiToken) {
            $apiToken->used++;
            $apiToken->save();
        }

        return $next($request);
    }
}
