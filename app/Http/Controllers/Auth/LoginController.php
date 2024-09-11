<?php

namespace App\Http\Controllers\Auth;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view ('auth.login');
    }

    // public function main()
    // {
    //     $products = Product::latest()->paginate(10);
    //     return view('products.index', compact('products'));
    // }

    // public function login(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'email' => 'required|email',
    //         // 'password' => 'required|min:8',
    //         'password' => 'required',
    //     ]);

    //     // Cek kredensial
    //     if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('/products'); // Redirect setelah login sukses
    //     }

    //     // Jika login gagal
    //     throw ValidationException::withMessages([
    //         'email' => __('auth.failed'),
    //     ]);
    // }

    public function login(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if (Auth::attempt($data)) {
                $request->session()->regenerate();
            
                return redirect()->intended('/products'); // User redirect ke halaman produk (view only)
            }
        }

        // Cek kredensial
        // if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
        //     $request->session()->regenerate();

        //     // Redirect berdasarkan role
        //     if (Auth::user()->role === 'admin') {
        //         return redirect()->intended('/products'); // Admin redirect ke halaman produk
        //     } else {
        //         return redirect()->intended('/user'); // User redirect ke halaman produk (view only)
        //     }
        // }

        // Jika login gagal
        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

