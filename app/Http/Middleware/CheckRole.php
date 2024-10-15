<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        //ga make api
        // $user = Auth::user();
        
        // if (!$user) {
        //     return redirect()->route('/');
        // }

        //make api
        // $user = auth()->guard('api')->user();
        // $user = JWTAuth::parseToken()->authenticate();
        
        
        $user = Auth::guard('api')->user();

        Log::info($user);
        // Jika pengguna tidak ditemukan, kembalikan respons JSON dengan status 401 (Unauthorized)
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $route = $request->route()->getName();

        if ($user->role === 'user') {
            if (in_array($route, ['products.index', 'products.show', 'products.data'])) {
                return $next($request);
            }

            return abort(403);
            // return redirect()->route('products.index')->with('error', 'Unauthorized access for users');
        }

        if ($user->role === 'admin') {

            return $next($request);
        }

        return abort(403);
        




        // if (Auth::check() && Auth::user()->role === $role) {
        //     return $next($request);
        // }

        // abort(403);

        // // Check if the user is authenticated
        // if (!Auth::check()) {
        //     return redirect('/');
        // }

        // $user = Auth::user();

        // // Check if user has the required role
        // if ($user->role !== $role) {
        //     abort(403);
        // }

        // // Allow the request to proceed if the user has the required role
        // return $next($request);

    }
}