<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegularUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            $message = 'Les administrateurs ne peuvent pas déclarer ni réclamer des objets.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()
                ->route('admin.dashboard')
                ->with('error', $message);
        }

        return $next($request);
    }
}
