<?php

namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $auth = Firebase::auth();

        try {
            $token = $request->bearerToken();
            $decodedToken = $auth->verifyIdToken($token, true);
            $request->user = $auth->getUser($decodedToken->claims()->get('sub'));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
