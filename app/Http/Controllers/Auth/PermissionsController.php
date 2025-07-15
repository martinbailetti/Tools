<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function getPaginated(Request $request)
    {




        $perPage = $request->query('per_page', $params['per_page'] ?? 10);
        $page = $request->query('page', $params['page'] ?? 1);
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "id");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "asc");

        $query = Permission::query();

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
            $result = Permission::orderBy('name')->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function get(Request $request)
    {
        $id = $request->query('id');

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }


        try {

            $result = Permission::findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function create(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];


        $name = $params["name"];


        try {
            $permission = Permission::create(['name' => $name]);
            return response()->json(['success' => true, 'result' => $permission]);
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
            $permission = Permission::findOrFail($id);
            $permission->name = $params["name"];
            $permission->save();
            return response()->json(['success' => true, 'result' => $permission]);
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
            Permission::destroy($id);
            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
