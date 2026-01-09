<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if (auth()->user()->type !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin only.');
        }
        
        return $next($request);
    }
}