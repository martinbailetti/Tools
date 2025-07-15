<?php

namespace App\Http\Controllers\Tasks;

use App\Exports\TasksExport;
use App\Http\Controllers\Controller;
use App\Models\Security\User;
use App\Models\Tasks\Client;
use App\Models\Tasks\UserTask;
use App\Models\Tasks\UserTasksFile;
use App\Models\Tasks\UserTasksMachine;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TasksController extends Controller
{


    public function get(Request $request)
    {

        $queryParams = $request->query();

        $params = [];
        parse_str(http_build_query($queryParams), $params);

        $id = $request->query('id');


        try {
            $result = UserTask::select('user_tasks.*', 'users.name', 'clients.name as client_name')
                ->where('user_tasks.id', $id)
                ->leftJoin("users", "users.id", "=", "user_tasks.user_id")
                ->leftJoin('clients', 'clients.id', '=', 'user_tasks.client')->first();


            $result->machines = UserTasksMachine::select("machine")->where("user_tasks_id", $id)->get();
            $result->files = UserTasksFile::select("path", "name")->where("user_tasks_id", $id)->get();

            $result->machines = $result->machines->pluck('machine');
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
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "task_datetime");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "desc");


        $query = UserTask::select('user_tasks.*', 'users.name', 'clients.name as client_name')
            ->leftJoin("users", "users.id", "=", "user_tasks.user_id");

        $query->leftJoin('clients', 'clients.id', '=', 'user_tasks.client');

        $query->leftJoin('user_tasks_machines', 'user_tasks.id', '=', 'user_tasks_machines.user_tasks_id')
            ->selectRaw('GROUP_CONCAT(user_tasks_machines.machine) as machines')
            ->groupBy('clients.name', 'user_tasks.id', 'user_id', 'subject', 'description', 'client', 'client_contact', 'task_datetime', 'task_hours', 'fault_of', 'resolved', 'users.name', 'user_tasks.created_at', 'user_tasks.updated_at');

        $filters = $request->query('filters');
        if ($filters) {


            foreach ($filters as $filter) {

                if ($filter["id"] == 'subject') {
                    $query->where('subject', $filter["value"]);
                } else if ($filter["id"] == 'client') {
                    $query->where('user_tasks.client', $filter["value"]);
                } else if ($filter["id"] == 'client_contact') {
                    $query->where('client_contact', $filter["value"]);
                } else if ($filter["id"] == 'fault_of') {
                    $query->where('fault_of', $filter["value"]);
                } else if ($filter["id"] == 'machine') {
                    $query->where('machine', $filter["value"]);
                } else if ($filter["id"] == 'task_datetime') {

                    if ($filter["value"]["from"]) {
                        $query->where('task_datetime', ">=", $filter["value"]["from"]);
                    }
                    if ($filter["value"]["to"]) {
                        $query->where('task_datetime', "<=", $filter["value"]["to"]);
                    }
                } else if ($filter["id"] == 'user') {
                    $query->where('user_id', $filter["value"]);
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

    public function getClients(Request $request)
    {

        try {
            $result = Client::orderBy("name", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSubjects(Request $request)
    {

        $search = $request->query('search');
        try {
            $result = UserTask::select("subject")->distinct()->where('subject', 'like', '%' . $search . '%')->orderBy("subject", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getMachines(Request $request)
    {

        $search = $request->query('search');
        try {
            $result = UserTasksMachine::select("machine")->distinct()->where('machine', 'like', '%' . $search . '%')->orderBy("machine", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getUsers(Request $request)
    {

        $search = $request->query('search');
        try {
            $result = User::select("name", "id")
                ->where('name', 'like', '%' . $search . '%')
                ->orderBy("name", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getClientContacts(Request $request)
    {

        $search = $request->query('search');
        try {
            $result = UserTask::select("client_contact")->distinct()->where('client_contact', 'like', '%' . $search . '%')->orderBy("client_contact", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $filters = $request->query('filters');
        return Excel::download(new TasksExport($filters), 'tasks_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
