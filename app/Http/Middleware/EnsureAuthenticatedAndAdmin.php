<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAuthenticatedAndAdmin
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
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
        }

        // Check if user is admin
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }
            
            return redirect()->route('home')
                ->with('error', 'Akses ditolak. Hanya administrator yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}