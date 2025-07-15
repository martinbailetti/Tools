<?php

namespace App\Exports;

use App\Models\Tasks\UserTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;



class TasksExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping, WithStrictNullComparison, WithEvents
{
    private $filters;
    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function map($obj): array
    {


        return [

            $obj->subject,
            $obj->client_name,
            $obj->client_contact,
            $obj->task_datetime,
            $obj->task_hours,
            $obj->name,
            $obj->machines,
            $obj->description
        ];
    }

    public function collection()
    {

        $query = UserTask::select('user_tasks.*', 'users.name', 'clients.name as client_name')
            ->leftJoin("users", "users.id", "=", "user_tasks.user_id");

        $query->leftJoin('clients', 'clients.id', '=', 'user_tasks.client');

        $query->leftJoin('user_tasks_machines', 'user_tasks.id', '=', 'user_tasks_machines.user_tasks_id')
            ->selectRaw('GROUP_CONCAT(user_tasks_machines.machine) as machines')
            ->groupBy('clients.name', 'user_tasks.id', 'user_id', 'subject', 'description', 'client', 'client_contact', 'task_datetime', 'task_hours', 'fault_of', 'resolved', 'users.name', 'user_tasks.created_at', 'user_tasks.updated_at');

        if ($this->filters) {
            foreach ($this->filters as $filter) {

                if ($filter["id"] == 'subject') {
                    $query->where('subject', $filter["value"]);
                } else if ($filter["id"] == 'client') {
                    $query->where('user_tasks.client', $filter["value"]);
                } else if ($filter["id"] == 'client_contact') {
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
                } else if ($filter["id"] == 'user') {
                    $query->where('user_id', $filter["value"]);
                }
            }
        }

        return $query->get();
    }



    public function headings(): array
    {
        return [

            "Subject",
            "Client",
            "Client Contact",
            "Date",
            "Hours",
            "User",
            "Machines",
            "Description"

        ];
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $headerColumns = 'A1:H1';
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
