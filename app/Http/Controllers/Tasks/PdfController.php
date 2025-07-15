<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Tasks\Client;
use App\Models\Tasks\UserTask;
use App\Models\Tasks\UserTasksFile;
use App\Models\Tasks\UserTasksMachine;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function get($id)
    {

        try {

            $result = UserTask::select('user_tasks.*', 'clients.name as client_name', 'users.name as user_name')
                ->leftJoin("clients", "clients.id", "=", "user_tasks.client")
                ->leftJoin("users", "users.id", "=", "user_tasks.user_id")
                ->where("user_tasks.id", $id)
                ->first();

            $files = UserTasksFile::select("path", "name")->where("user_tasks_id", $id)->get();
            $machines = UserTasksMachine::select("machine")->where("user_tasks_id", $id)->get();

            $data = [
                'task' => $result,
                'files' => $files,
                'machines' => $machines,
                'logo' => $machines
            ];


            $pdf = Pdf::loadView('pdf', $data);
            return $pdf->download('task.pdf');


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }

}
