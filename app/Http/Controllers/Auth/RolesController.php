<?php

namespace App\Http\Controllers\Auth;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Security\Role;

class RolesController extends Controller
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

        $query = Role::query();

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
            $result = Role::orderBy('name')->get();
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
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }


        try {
            $result = Role::with('permissions')->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function create(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];



        try {
            $role = Role::create(['name' => $params["name"], 'home_path' => $params["home_path"]]);
            $role->syncPermissions($params["permissions"]);
            return response()->json(['success' => true, 'result' => $role]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $id = $params["id"];

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }

        try {
            $role = Role::findOrFail($id);
            $role->name = $params["name"];
            $role->home_path = $params["home_path"];
            $role->save();
            $role->syncPermissions($params["permissions"]);
            return response()->json(['success' => true, 'result' => $role]);
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
            Role::destroy($id);
            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
