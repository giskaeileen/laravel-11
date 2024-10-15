<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     // Validate input
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Attempt to authenticate the user
    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         throw ValidationException::withMessages([
    //             'email' => __('auth.failed'),
    //         ]);
    //     }

    //     // Return the token on successful login
    //     return response()->json([
    //         'token' => $token,
    //     ], 200);
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
        
    //     if (!$token = Auth::guard('api')->attempt($credentials)) {
    //         return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    //     }

    //     $user = Auth::guard('api')->user();

    //     return response()->json([
    //         'user' => [
    //             'email' => auth()->guard('api')->user()->email,
    //             'name' => auth()->guard('api')->user()->name,
    //         ],
    //         'token' => $token,
    //     ], 200);

    // }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'user' => [
                'email' => auth()->guard('api')->user()->email,
                'name' => auth()->guard('api')->user()->name,
                'role' => auth()->guard('api')->user()->role,
            ],
            'token' => $token,
        ], 200);

    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();
    
        if (!$token) {
            return response()->json([
                'message' => 'Token not provided',
            ], 401);
        }

        try {
            $newToken = JWTAuth::parseToken($token)->refresh();

            return response()->json([
                'token' => $newToken,
                'message' => 'Access token generated successfully'
            ], 200);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Refresh token expired, please log in again'
            ], 401);
        }

    }


    // public function logout(Request $request)
    // {
    //     return response()->json(['message' => 'Successfully logged out'], 200);
    // }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Successfully logged out'], 200);
    }


    // public function logout(Request $request)
    // {
    //     try {
    //         // Invalidate current access token
    //         JWTAuth::invalidate(JWTAuth::getToken());
    //         return response()->json(['message' => 'Successfully logged out'], 200);
    //     } catch (JWTException $exception) {
    //         return response()->json([
    //             'message' => 'Failed to logout, please try again.'
    //         ], 500);
    //     }
    // }

        /**
     * Handle the incoming request.
     */
    // public function login(Request $request)
    // {
        
    //     $credentials = $request->only('email', 'password');
        
    //     if (!$token = Auth::guard('api')->attempt($credentials)) {
    //         return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
    //     }

    //     // $user = Auth::guard('api')->user();


    //     return response()->json([
    //         'user' => [
    //             'email' => auth()->guard('api')->user()->email,
    //             'name' => auth()->guard('api')->user()->name,
    //         ],
    //         'token' => $token,
    //     ], 200);
    // }

    // public function login(Request $request)
    // {
    //     // Validate input
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Attempt to authenticate the user
    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return response()->json([
    //             'error' => 'Invalid credentials'
    //         ], 401);
    //     }

    //     // Return the token on successful login
    //     return response()->json([
    //         'token' => $token,
    //     ]);
    // }


    // public function login(Request $request)
    // {
    //     // Validate input
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Attempt to authenticate the user
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         $token = JWTAuth::fromUser($user);

    //         // Redirect to /products with token in session
    //         return redirect('/products')->with('token', $token);
    //     }

    //     // If authentication fails
    //     return redirect('/login')->withErrors([
    //         'email' => __('auth.failed'),
    //     ]);
    // }

    // public function login(Request $request)
    // {
    //     // Validate input
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Attempt to authenticate the user with remember option
    //     $remember = $request->has('remember');

    //     if (Auth::attempt($credentials, $remember)) {
    //         $user = Auth::user();
    //         $token = JWTAuth::fromUser($user);

    //         // Redirect to /products with token in session
    //         return redirect('/products')->with('token', $token);
    //     }

    //     // If authentication fails
    //     return redirect('/login')->withErrors([
    //         'email' => __('auth.failed'),
    //     ]);
    // }


    /**
     * Handle the incoming request.
     */
    // public function logout(Request $request)
    // {
    //     return response()->json(['message' => 'Successfully logged out']);
    // }
}
