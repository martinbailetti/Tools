<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $email = $params["email"];
        
        $status = Password::sendResetLink(
            [
                'email' => $email
            ]
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }
}
