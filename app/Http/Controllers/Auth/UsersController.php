<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\Security\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function getPaginated(Request $request)
    {


        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);


        $perPage = $request->query('per_page', $params['per_page'] ?? 10);
        $page = $request->query('page', $params['page'] ?? 1);
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "id");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "asc");

        $query = User::query();

        $query->select('users.id', 'users.name', 'users.email', 'users.timezone', 'users.language_code', 'clients.name as client_name', 'users.created_at', 'users.updated_at', 'users.client');
        $query->leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->leftJoin('clients', 'clients.id', '=', 'users.client')
            ->selectRaw('GROUP_CONCAT(roles.name) as roles')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.timezone', 'users.language_code', 'clients.name', 'users.created_at', 'users.updated_at', 'users.client');



        $filters = $request->query('filters');

        if ($filters) {

            foreach ($filters as $filter) {

                if ($filter["id"] == 'name_multi') {
                    $query->where(function ($query) use ($filter) {
                        foreach ($filter["value"] as $name) {
                            $query->orWhere('name', "like", "%" . $name . "%");
                        }
                    });
                }
            }
        }


        try {
            $result = $query->orderBy($sort_by, $sort_direction)
                ->paginate($perPage, ['*'], 'page', $page);



        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getAll()
    {
        try {
            $result = User::orderBy('name')->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function get(Request $request)
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
    public function create(Request $request)
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

    public function update(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];
        $client = isset($params["client"]) ?$params["client"]:null;

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

    
    public function updatePassword(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $id = $params["id"];
        $password = $params["password"];

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "bad_request"]);
        }



        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($password);
            $user->save();
            return response()->json(['success' => true, 'result' => $user]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function delete(Request $request)
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
