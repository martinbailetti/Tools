<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Security\User;
use App\Models\Tasks\Client;
use App\Models\Tasks\UserTask;
use Illuminate\Http\Request;

class ClientsController extends Controller
{


    public function get(Request $request)
    {

        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);

        $id = $request->query('id');


        try {
            $result = Client::where('id', $id)->first();


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function getPaginated(Request $request)
    {

        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);


        $perPage = $request->query('per_page', $params['per_page'] ?? 10);
        $page = $request->query('page', $params['page'] ?? 1);
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "name");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "desc");


        $query = Client::query();

        $filters = $request->query('filters');
        if ($filters) {

            foreach ($filters as $filter) {

                if ($filter["id"] == 'name') {
                    $query->where('name', $filter["name"]);
                }
            }
        }

        try {
            $result = $query
                ->orderBy($sort_by, $sort_direction)
                ->paginate($perPage, ['*'], 'page', $page);
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
        $month_hours = $params["month_hours"];

        try {

            $result = Client::create([
                'name' => $name,
                'month_hours' => $month_hours

            ]);



            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {

        $bodyParams = $request->all();
        $params = $bodyParams["params"];

        $id = $params["id"];

        $name = $params["name"];
        $month_hours = $params["month_hours"];
        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }


        try {

            $result = Client::where("id", $id)->update([
                'name' => $name,
                'month_hours' => $month_hours
            ]);



            return response()->json(['success' => true, 'result' => $result]);
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

        $qc = UserTask::where('client', $id)->count();
        $qu = User::where('client', $id)->count();

        if ($qc > 0 || $qu > 0) {
            return response()->json(['success' => false, 'message' => "Client is in use"]);
        }


        try {
            Client::destroy($id);
            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
