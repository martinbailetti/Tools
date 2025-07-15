<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Security\User;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{

    public function update(Request $request)
    {
        $user = auth()->user();

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $requiredParams = ['name',  'timezone', 'language_code'];
        foreach ($requiredParams as $param) {
            if (empty($params[$param])) {
                return response()->json(['success' => false, 'message' => "$param is required"]);
            }
        }

        if (User::where('name', $params["name"])->where('id', '!=', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => "name_taken"]);
        }

/*         if (User::where('email', $params["email"])->where('id', '!=', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => "email_taken"]);
        } */

        try {
            $user->name = $params["name"];
        /*     $user->email = $params["email"]; */
            $user->timezone = $params["timezone"];
            $user->language_code = $params["language_code"];
            $user->save();
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $password = $params["password"];
        $password_confirmation = $params["password_confirmation"];


        if ($password != $password_confirmation) {
            return response()->json(['success' => false, 'message' => "Passwords do not match"]);
        }

        try {
            $user->password = Hash::make($password);
            $user->save();
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
