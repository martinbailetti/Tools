<?php

namespace App\Http\Controllers\Tasks;

use App\Exports\ClientHoursExport;
use App\Exports\HoursExport;
use App\Http\Controllers\Controller;
use App\Models\Tasks\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClientHoursController extends Controller
{

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


        $query = UserTask::select(
            'clients.name as client',
            'clients.month_hours',
            DB::raw("DATE_FORMAT(task_datetime, '%Y-%m') as month"),
            DB::raw("SUM(CASE WHEN fault_of = 'client' THEN task_hours ELSE 0 END) AS total_hours"))
      
            ->leftJoin('clients', 'clients.id', '=', 'user_tasks.client')
            ->where('user_tasks.client', $user->client)
            ->groupBy('clients.name', 'clients.month_hours', 'month')
            ->orderBy('month', "desc")
            ->orderBy('client');




        $filters = $request->query('filters');
        if ($filters) {


            foreach ($filters as $filter) {

                if ($filter["id"] == 'month') {

                    if ($filter["value"]["from"]) {
                        $query->where(DB::raw("DATE_FORMAT(task_datetime, '%Y-%m')"), ">=", $filter["value"]["from"]);
                    }
                    if ($filter["value"]["to"]) {
                        $query->where(DB::raw("DATE_FORMAT(task_datetime, '%Y-%m')"), "<=", $filter["value"]["to"]);
                    }
                }
            }
        }

        try {
            $result = $query
                ->paginate($perPage, ['*'], 'page', $page);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function export(Request $request)
    {
        $filters = $request->query('filters');
        return Excel::download(new ClientHoursExport($filters), 'hours_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
