<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            // Auth::logoutOtherDevices($request->password);

            // $request->session()->regenerate();
            $request->user()->tokens()->delete();
            $token = $request->user()->createToken($request->email . $request->password);

            return response()->json(
                [
                    'status' => true,
                    'msg' => 'login success',
                    'token' => $token->plainTextToken
                ]
            );
        }
    }

    public function logutService(Request $request)
    {
        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        $request->user()->currentAccessToken()->delete();

        return response()->json(
            [
                'status' => true,
                'msg' => 'logout success',
            ]
        );
    }
}
