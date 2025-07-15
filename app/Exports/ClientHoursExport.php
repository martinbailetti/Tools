<?php

namespace App\Exports;

use App\Models\Tasks\UserTask;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;



class ClientHoursExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithStrictNullComparison, WithEvents
{
    private $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function map($obj): array
    {


        return [

            $obj->client,
            $obj->month,
            $obj->month_hours,
            $obj->total_hours,
            $obj->month_hours - $obj->total_hours
        ];
    }

    public function collection()
    {

        $user = auth()->user();

        if (!$user->hasRole('client')) {
            return [];
        }


        if (!$user->client) {
            return [];
        }
      

        $query = UserTask::select(
            'clients.name as client',
            'clients.month_hours',
            DB::raw("DATE_FORMAT(task_datetime, '%Y-%m') as month"),
            DB::raw("SUM(CASE WHEN fault_of = 'client' THEN task_hours ELSE 0 END) as total_hours")
        )
            ->leftJoin('clients', 'clients.id', '=', 'user_tasks.client')
            ->where('user_tasks.client', $user->client)
            ->groupBy('clients.name', 'clients.month_hours', 'month')
            ->orderBy('month', "desc")
            ->orderBy('client');


        if ($this->filters) {
            foreach ($this->filters as $filter) {

             
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

        return $query->get();
    }



    public function headings(): array
    {
        return [

            "Client",
            "Month",
            "Total Hours",
            "Consumed Hours",
            "Remaining Hours"

        ];
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $headerColumns = 'A1:E1';
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle($headerColumns)->getFill()
                    ->applyFromArray(
                        [
                            'fillType' => 'solid',
                            'rotation' => 0,
                            'color' => ['rgb' => '006400'],

                        ]
                    );
                $sheet->getStyle($headerColumns)
                    ->applyFromArray(
                        [
                            'font' => array(
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF'],
                            )
                        ]
                    );
            },
        ];
    }
}
