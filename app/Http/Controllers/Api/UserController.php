<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getUser() {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        $name = $user->name;

        return response()->json([
            'name' => $name
        ], 200);
    }

    public function getUserData(Request $request) {
        $id = JWTAuth::getPayload()->get('id');

        $user =  User::findOrFail($id);

        return response()->json([
            'message' => 'Data dikirim',
            'user' => $user,
        ], 200);
    }

    public function uploadImage(Request $request) {
        
        $request->validate([
            'photo' => 'image|mimes:jpeg,jpg,png',
        ]);


        $id = JWTAuth::getPayload()->get('id');

        $user = User::findOrFail($id);

        
        if ($user->photo) {
            Storage::delete('public/user/'.$user->photo);
        }


        $photo = $request->file('photo');
        $photoName = $id .'_'. Str::uuid() .'.' . $photo->getClientOriginalExtension();
        $photo->storeAs('public/user', $photoName);

        $user->photo = $photoName;
        $user->save();

        return response()->json([
            'message' => 'Photo berhasil di upload'
        ], 200);
    }

    public function updateUserProfile (Request $request) {

        $request->validate([
            'email'     => 'required|email',
            'old_password'  => 'required',
            'new_password'  => 'required',
            'confirm_password' => 'required',
        ]);

        $id = JWTAuth::getPayload()->get('id');

        $user = User::findOrFail($id);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'Password lama tidak sesuai'], 400);
        }

        if ($request->new_password !== $request->confirm_password) {
            return response()->json(['error' => 'Konfirmasi password baru salah'], 400);            
        }

        $user->email = $request->email;
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Data berhasil diperbarui',
            'user'    => $user,
        ], 200);
    }
}
