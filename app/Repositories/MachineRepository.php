<?php

namespace App\Repositories;

use App\Models\Hardwarelink\License;
use Illuminate\Support\Facades\DB;

class MachineRepository
{

    private $pingback = '';

    public function __construct()
    {
        $this->pingback = DB::connection('mysql_pingback')->getDatabaseName();
    }



    public function getMachinesQuery($group_id)
    {
        $query = License::select([
            'licenses.MachineId',
            'licenses.GroupId',
            DB::raw("(select max(Position) from licenses as l where l.GroupId=licenses.GroupId and MachineId=licenses.MachineId) as Position"),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId) as Total"),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId and Active=1) as Active"),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.MachineId=licenses.MachineId and l.ReportedActive = 1) as ReportedActive'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.MachineId=licenses.MachineId and l.Active = 1 AND l.WhyActive NOT IN (257, 263)) as Validated'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.MachineId=licenses.MachineId and l.ActionRequest = 2 AND l.Active = 1) as Validating'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.MachineId=licenses.MachineId and l.WhyActive IN (257, 263)) as Expiration'),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId and SyncActionRequest=1) as Deleting"),
            DB::raw("(SELECT MAX(LastPingTimeStamp) FROM " . $this->pingback . ".requests WHERE licenses.GroupId = GroupIdLicense) as LastPingTimeStamp")
        ])
            ->where('licenses.GroupId', $group_id)

            ->groupBy('licenses.MachineId', 'licenses.GroupId');


        return $query;
    }




    public function getSimpleMachinesQuery($group_id="")
    {
        $query = License::select([
            'licenses.MachineId',
            'licenses.GroupId',
            'licenses.Position',
        ]);

        if ($group_id != "") {
            $query->where('licenses.GroupId', $group_id);
        }
        $query->groupBy('licenses.MachineId', 'licenses.GroupId', 'licenses.Position');


        return $query;
    }



    public function getMachine($GroupId, $MachineQuery)
    {
        $query = $this->getMachinesQuery($GroupId);

        $query->where('licenses.MachineId', $MachineQuery);

        return $query->first();
    }




}
