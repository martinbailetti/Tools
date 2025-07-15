<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Security\User;
use Spatie\Activitylog\Traits\LogsActivity;

class AuthController extends Controller
{
    use LogsActivity;
    public function getUser()
    {
        $user = Auth::user();
        $roles = $user->roles;
        $permissions = $roles->flatMap(function ($role) {
            return $role->permissions;
        })->unique();
        $user->permissions = $permissions;




        activity('Login')
            ->causedBy($user)
            ->withProperties([
                'user' => $user,
            ])
            ->log('Login');


        return response()->json(['user' => $user]);
    }



}
