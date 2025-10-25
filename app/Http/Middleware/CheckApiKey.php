<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiKey
{
    public function handle(Request $request, Closure $next) {
    $apiKey = $request->header('x-k');
    $validKey = 'unrelated';

    if (empty($apiKey) || $apiKey !== $validKey) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return $next($request);
    }

}
