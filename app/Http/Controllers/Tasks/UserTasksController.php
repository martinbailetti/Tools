<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Client;
use App\Models\Tasks\UserTask;
use App\Models\Tasks\UserTasksFile;
use App\Models\Tasks\UserTasksMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTasksController extends Controller
{


    public function get(Request $request)
    {

        $user = auth()->user();

        $id = $request->query('id');

        if (empty($id)) {
            return response()->json(['success' => false, 'message' => 'ID is required']);
        }

        try {

            $result = UserTask::select('user_tasks.*', 'users.name')
                ->where('user_tasks.id', $id)
                ->where('user_id', $user->id)
                ->leftJoin("users", "users.id", "=", "user_tasks.user_id")->first();

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


        $perPage = $request->query('per_page', $params['per_page'] ?? 10);
        $page = $request->query('page', $params['page'] ?? 1);
        $sort_by = $request->query('sort_by', $params['sort_by'] ?? "LastPingTimeStamp");
        $sort_direction = $request->query('sort_direction', $params['sort_direction'] ?? "desc");


        $query = UserTask::select('user_tasks.*', 'clients.name as client_name', DB::raw("(select count(id) from user_tasks_files where user_tasks_files.user_tasks_id=user_tasks.id) as files"));


        $query->leftJoin('clients', 'clients.id', '=', 'user_tasks.client');



        $query->leftJoin('user_tasks_machines', 'user_tasks.id', '=', 'user_tasks_machines.user_tasks_id')
            ->selectRaw('GROUP_CONCAT(user_tasks_machines.machine) as machines')
            ->groupBy('user_tasks.id', 'user_id', 'subject', 'description', 'client', 'client_contact', 'task_datetime', 'task_hours', 'fault_of', 'resolved', 'clients.name', 'user_tasks.created_at', 'user_tasks.updated_at');


        $query->where("user_id", $user->id);

        $filters = $request->query('filters');
        if ($filters) {

            foreach ($filters as $filter) {

                if ($filter["id"] == 'client_contact') {
                    $query->where('client_contact', $filter["value"]);
                } else if ($filter["id"] == 'subject') {
                    $query->where('subject', $filter["value"]);
                } else if ($filter["id"] == 'client') {
                    $query->where('client', $filter["value"]);
                } else if ($filter["id"] == 'machine') {
                    $query->where('machine', $filter["value"]);
                } else if ($filter["id"] == 'fault_of') {
                    $query->where('fault_of', $filter["value"]);
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
    public function create(Request $request)
    {
        $user = auth()->user();

        $params = json_decode($request->input('params'), true);

        if (!$params) {
            return response()->json(['success' => false, 'message' => "Params are required"]);
        }

        $subject = $params["subject"];
        $description = !empty($params["description"]) ? $params["description"] : "";
        $client = $params["client"];
        $client_contact = !empty($params["client_contact"]) ? $params["client_contact"] : null;
        $task_datetime = $params["task_datetime"];
        $task_hours = $params["task_hours"];
        $machines = isset($params["machines"]) ? $params["machines"] : [];
        $fault_of = $params["fault_of"];
        $resolved = $params["resolved"];


        try {

            $result = UserTask::create([
                'subject' => $subject,
                'user_id' => $user->id,
                'description' => $description,
                'client' => $client,
                'client_contact' => $client_contact,
                'task_datetime' => $task_datetime . ":00",
                'task_hours' => $task_hours,
                'fault_of' => $fault_of,
                'resolved' => $resolved

            ]);

            foreach ($machines as $machine) {
                UserTasksMachine::create([
                    'machine' => $machine,
                    'user_tasks_id' => $result->id
                ]);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $fileName); // Guarda en public/uploads
                    $path = 'uploads/' . $fileName; // Devuelve la URL pública
                    UserTasksFile::create([
                        'user_tasks_id' => $result->id,
                        'path' => $path,
                        'name' => $file->getClientOriginalName()
                    ]);
                }
            }

            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {

        $params = json_decode($request->input('params'), true);

        if (!$params) {
            return response()->json(['success' => false, 'message' => "Params are required"]);
        }
        $id = $params["id"];

        $subject = $params["subject"];
        $description = !empty($params["description"]) ? $params["description"] : "";
        $client = $params["client"];
        $client_contact = !empty($params["client_contact"]) ? $params["client_contact"] : null;
        $task_datetime = $params["task_datetime"];
        $task_hours = $params["task_hours"];
        $machines = isset($params["machines"]) ? $params["machines"] : [];
        $fault_of = $params["fault_of"];
        $resolved = $params["resolved"];
        $files = $params["files"];



        if (empty($id)) {
            return response()->json(['success' => false, 'message' => "ID is required"]);
        }


        try {

            $result = UserTask::where("id", $id)->update([
                'subject' => $subject,
                'description' => $description,
                'client' => $client,
                'client_contact' => $client_contact,
                'task_datetime' => $task_datetime,
                'task_hours' => $task_hours,
                'fault_of' => $fault_of,
                'resolved' => $resolved
            ]);

            UserTasksMachine::where("user_tasks_id", $id)->delete();

            foreach ($machines as $machine) {
                UserTasksMachine::create([
                    'machine' => $machine,
                    'user_tasks_id' => $id
                ]);
            }



            $paths = array_map(function ($file) {
                return $file['path'];
            }, $files);

            $filesToDelete = UserTasksFile::where("user_tasks_id", $id)->whereNotIn("path", $paths)->get();

            foreach ($filesToDelete as $file) {


                $filePath = public_path($file->path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            UserTasksFile::where("user_tasks_id", $id)->whereNotIn("path", $paths)->delete();

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads'), $fileName); // Guarda en public/uploads
                    $path = 'uploads/' . $fileName; // Devuelve la URL pública
                    UserTasksFile::create([
                        'user_tasks_id' => $id,
                        'path' => $path,
                        'name' => $file->getClientOriginalName()
                    ]);
                }
            }
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
        try {
            UserTask::destroy($id);
            return response()->json(['success' => true, 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function getUserClients(Request $request)
    {

        $user = auth()->user();
        $search = $request->query('search');
        try {
            $result = Client::select("id", "name")
                ->distinct()
                ->where('client', 'like', '%' . $search . '%')
                ->where('user_id', $user->id)
                ->join("suer_tasks", "clients.id", "=", "user_tasks.client")
                ->orderBy("client", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getUserSubjects(Request $request)
    {

        $user = auth()->user();
        $search = $request->query('search');
        try {
            $result = UserTask::select("subject")
                ->distinct()
                ->where('subject', 'like', '%' . $search . '%')
                ->where('user_id', $user->id)
                ->orderBy("subject", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getUserClientContacts(Request $request)
    {

        $user = auth()->user();
        $search = $request->query('search');
        try {
            $result = UserTask::select("client_contact")
                ->distinct()
                ->where('client_contact', 'like', '%' . $search . '%')
                ->where('user_id', $user->id)
                ->orderBy("client_contact", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function getUserMachines(Request $request)
    {

        $user = auth()->user();
        $search = $request->query('search');
        try {
            $result = UserTasksMachine::select("machine")
                ->distinct()

                ->join("user_tasks", "user_tasks.id", "=", "user_tasks_machines.user_tasks_id")
                ->where('machine', 'like', '%' . $search . '%')
                ->where('user_id', $user->id)
                ->orderBy("machine", "asc")->get();
            return response()->json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


}
