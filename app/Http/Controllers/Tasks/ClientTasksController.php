<?php

namespace App\Http\Controllers\Tasks;

use App\Exports\ClientTasksExport;
use App\Exports\TasksExport;
use App\Http\Controllers\Controller;
use App\Models\Security\User;
use App\Models\Tasks\UserTask;
use App\Models\Tasks\UserTasksFile;
use App\Models\Tasks\UserTasksMachine;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientTasksController extends Controller
{


    public function get(Request $request)
    {

        $user = auth()->user();

        if (!$user->hasRole('client')) {
            return response()->json(['success' => false, 'message' => "wrong permissions"]);
        }


        if (!$user->client) {
            return response()->json(['success' => false, 'message' => "wrong client"]);
        }

        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);

        $id = $request->query('id');


        try {
            $result = UserTask::select('user_tasks.*', 'users.name', 'clients.name as client_name')
                ->where('user_tasks.id', $id)
                ->where('user_tasks.client', $user->client)
                ->leftJoin("users", "users.id", "=", "user_tasks.user_id")
                ->leftJoin('clients', 'clients.id', '=', 'user_tasks.client')->first();

                $result->files = UserTasksFile::select("path", "name")->where("user_tasks_id", $id)->get();

                $result->machines = UserTasksMachine::select("machine")->where("user_tasks_id", $id)->get();

                $result->machines = $result->machines->pluck('machine');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function getPaginated(Request $request)
    {

        $user = auth()->user();

        if (!$user->hasRole('client')) {
            return response()->json(['success' => false, 'message' => "wrong permissions"]);
        }


        if (!$user->client) {
            return response()->json(['success' => false, 'message' => "wrong client"]);
        }
        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);


        $perPage = $request->query('per_page', $params['per_page'] ?? 10);
        $page = $request->query('page', $params['page'] ?? 1);
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "task_datetime");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "desc");


        $query = UserTask::select('user_tasks.*', 'users.name', 'clients.name as client_name')
            ->leftJoin("users", "users.id", "=", "user_tasks.user_id");

        $query->leftJoin('clients', 'clients.id', '=', 'user_tasks.client');
        $query->where('user_tasks.client', $user->client);

        $query->leftJoin('user_tasks_machines', 'user_tasks.id', '=', 'user_tasks_machines.user_tasks_id')
            ->selectRaw('GROUP_CONCAT(user_tasks_machines.machine) as machines')
            ->groupBy('clients.name', 'user_tasks.id', 'user_id', 'subject', 'description', 'client', 'client_contact', 'task_datetime', 'task_hours', 'fault_of', 'resolved', 'users.name', 'user_tasks.created_at', 'user_tasks.updated_at');

        $filters = $request->query('filters');
        if ($filters) {


            foreach ($filters as $filter) {

                if ($filter["id"] == 'client_contact') {
                    $query->where('client_contact', $filter["value"]);
                } else if ($filter["id"] == 'machine') {
                    $query->where('machine', $filter["value"]);
                } else if ($filter["id"] == 'task_datetime') {

                    if ($filter["value"]["from"]) {
                        $query->where('task_datetime', ">=", $filter["value"]["from"]);
                    }
                    if ($filter["value"]["to"]) {
                        $query->where('task_datetime', "<=", $filter["value"]["to"]);
                    }
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

    public function getMachines(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('client')) {
            return response()->json(['success' => false, 'message' => "wrong permissions"]);
        }


        if (!$user->client) {
            return response()->json(['success' => false, 'message' => "wrong client"]);
        }

        $search = $request->query('search');
        try {
            $result = UserTasksMachine::select("machine")->distinct()
            ->where('machine', 'like', '%' . $search . '%')
            ->where('user_tasks.client', $user->client)
            ->join("user_tasks", "user_tasks.id", "=", "user_tasks_machines.user_tasks_id")
            ->orderBy("machine", "asc")->get();

            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function getClientContacts(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('client')) {
            return response()->json(['success' => false, 'message' => "wrong permissions"]);
        }


        if (!$user->client) {
            return response()->json(['success' => false, 'message' => "wrong client"]);
        }

        $search = $request->query('search');
        try {
            $result = UserTask::select("client_contact")->distinct()
            ->where('client_contact', 'like', '%' . $search . '%')
            ->where('client', $user->client)
            ->orderBy("client_contact", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $filters = $request->query('filters');
        return Excel::download(new ClientTasksExport($filters), 'tasks_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
