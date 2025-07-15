<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Security\User;
use App\Http\Controllers\Controller;
use App\Models\Security\Role;
use App\Models\Tasks\Client;
use Illuminate\Support\Facades\Hash;

class LinkSPVController extends Controller
{

    public function __construct()
    {
        $this->middleware('link.spv.token');
    }
    public function getRoles()
    {
        try {
            $result = Role::orderBy('name')->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getClients(Request $request)
    {

        try {
            $result = Client::orderBy("name", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getUser(Request $request)
    {

        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);


        $id = $request->query('id');

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "bad_request"]);
        }


        try {

            $result = User::findOrFail($id)->load('roles');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "page_not_found"]);
        }


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function createUser(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];
        $client = isset($params["client"]) ? $params["client"] : null;

        if (User::where('name', $params["name"])->exists()) {
            return response()->json(['success' => false, 'message' => "Name is already taken by another user"]);
        }

        if (User::where('email', $params["email"])->exists()) {
            return response()->json(['success' => false, 'message' => "Email is already taken by another user"]);
        }
        try {
            $user = User::create(['name' => $params["name"], 'email' => $params["email"], 'timezone' => $params["timezone"], 'language_code' => $params["language_code"], 'client' => $client, 'password' => Hash::make($params["password"])]);
            $user->syncRoles($params["roles"]);
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateUser(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];
        $client = isset($params["client"]) ? $params["client"] : null;

        $id = $params["id"];

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "bad_request"]);
        }

        if (User::where('name', $params["name"])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => "name_taken"]);
        }

        if (User::where('email', $params["email"])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => "email_taken"]);
        }

        try {
            $user = User::findOrFail($id);
            $user->name = $params["name"];
            $user->email = $params["email"];
            $user->timezone = $params["timezone"];
            $user->language_code = $params["language_code"];
            $user->client = $client;
            $user->save();
            $user->syncRoles($params["roles"]);
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function deleteUser(Request $request)
    {
        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $id = $params["id"];

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }
        try {
            User::destroy($id);
            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
