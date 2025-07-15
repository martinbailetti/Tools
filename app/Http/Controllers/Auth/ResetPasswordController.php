<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $email = $params["email"];
        $token = $params["token"];
        $password = $params["password"];
        $password_confirmation = $params["password_confirmation"];


        $status = Password::reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $password,
                'password_confirmation' => $password_confirmation
            ],
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }
}
