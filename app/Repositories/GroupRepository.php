<?php

namespace App\Repositories;

use App\Models\Hardwarelink\Ip;
use App\Models\Hardwarelink\License;
use Illuminate\Support\Facades\DB;

class GroupRepository
{

    private $pingback = '';

    public function __construct()
    {
        $this->pingback = DB::connection('mysql_pingback')->getDatabaseName();
    }
/*
(
SELECT MIN(`hardwarelink`.`licenses`.`Expiration`)
FROM `hardwarelink`.`licenses`
WHERE `hardwarelink`.`licenses`.`GroupId` = `l`.`GroupId` AND `hardwarelink`.`licenses`.`WhyActive` IN (257,263)) AS `Expiration_Date`

*/
    public function getGroupsQuery()
    {
        $ips = Ip::select([
            'IP'
        ])->get();

        $ipsArray = $ips->pluck('IP')->toArray();

        /*    $main = License::connection()->getDatabaseName();
         */
        $query = License::select([
            'licenses.GroupId',
            'requests.AppTitle',
            'requests.AppVersion',
            'requests.AppDateTime',
            'requests.LastPingTimeStamp',
            'requests.Owner',
            'requests.Description',
            DB::raw("SUBSTRING(requests.GroupId, LOCATE('_', requests.GroupId, LOCATE('_', requests.GroupId) + 1) + 1) AS SerialNumber"),
            'requests.IP',
            DB::raw('MAX(licenses.LastDate) as LastDate'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId) as Total'),
            DB::raw('(SELECT MIN(l.Expiration) FROM licenses l WHERE licenses.GroupId = l.GroupId AND l.WhyActive IN (257,263)) AS Expiration_Date'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.Active = 1) as Active'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.Ignored = 1) as Ignored'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.ReportedActive = 1) as ReportedActive'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.Active = 1 AND l.WhyActive NOT IN (257, 263)) as Validated'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.ActionRequest = 2 AND l.Active = 1) as Validating'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and l.WhyActive IN (257, 263)) as Expiration'),
            DB::raw('(SELECT COUNT(distinct l.MachineId) FROM licenses l WHERE licenses.GroupId = l.GroupId) as MachineIdCount'),
            DB::raw('CASE WHEN requests.IP IN (' . implode(',', array_map(function ($ip) {
                return "'" . $ip . "'";
            }, $ipsArray)) . ') THEN 1 ELSE 0 END as FactoryIP')


        ])
            ->join($this->pingback . '.requests as requests', 'licenses.GroupId', '=', 'requests.GroupIdLicense')

            ->whereRaw('requests.LastPingTimeStamp = (select max(pb2.LastPingTimeStamp) from ' . $this->pingback . '.requests pb2 where licenses.GroupId = pb2.GroupIdLicense)')
            ->groupBy([
                'licenses.GroupId',
                'requests.AppTitle',
                'requests.AppVersion',
                'requests.AppDateTime',
                'requests.LastPingTimeStamp',
                'requests.Owner',
                'requests.Description',
                'requests.GroupId',
                'requests.IP'
            ]);



        return $query;
    }


    public function getFilteredGroupsQuery($filters)
    {

        $query = $this->getGroupsQuery();

        if ($filters) {
            foreach ($filters as $filter) {

                if ($filter["id"] == 'GroupId_autofill_multi') {
                    $query->whereIn('licenses.GroupId', $filter["value"]);
                }
                if ($filter["id"] == 'SerialNumber_autofill_multi') {
                    $query->where(function ($query) use ($filter) {
                        foreach ($filter["value"] as $value) {
                            $query->orWhere(DB::raw("SUBSTRING(requests.GroupId, LOCATE('_', requests.GroupId, LOCATE('_', requests.GroupId) + 1) + 1)"), "=", $value);
                        }
                    });
                }
                if ($filter["id"] == 'AppTitle_autofill_multi') {
                    $query->whereIn('requests.AppTitle', $filter["value"]);
                }
                if ($filter["id"] == 'LastDate_date_range') {


                    $from = $filter["value"][0]["from"];
                    $to = $filter["value"][0]["to"];


                    if (!empty($from)) {
                        $query->where('LastDate', '>=', $from);
                    }
                    if (!empty($to)) {
                        $to = date('Y-m-d', strtotime($to . ' +1 day'));
                        $query->where('LastDate', '<=', $to);
                    }
                }
                if ($filter["id"] == 'LastPingTimeStamp_date_range') {


                    $from = $filter["value"]["from"];
                    $to = $filter["value"]["to"];


                    if (!empty($from)) {
                        $query->where('LastPingTimeStamp', '>=', $from);
                    }
                    if (!empty($to)) {
                        $to = date('Y-m-d', strtotime($to . ' +1 day'));
                        $query->where('LastPingTimeStamp', '<=', $to);
                    }
                }
                if ($filter["id"] == 'IP_autofill_multi') {
                    $query->whereIn('requests.IP', $filter["value"]);
                }
                if ($filter["id"] == 'factory_ip') {
                    $ips = Ip::select([
                        'IP'
                    ])->get();

                    $ipsArray = $ips->pluck('IP')->toArray();

                    if ($filter["value"] == 1) {
                        $query->whereIn("pb.IP", $ipsArray);
                    } else {
                        $query->whereNotIn("pb.IP", $ipsArray);
                    }
                }
            }
        }


        return $query;
    }


    public function getGroupsSimpleQuery()
    {

        $query = License::select([
            'licenses.GroupId as GroupId',
            'requests.AppTitle as AppTitle',
            'requests.AppVersion as AppVersion',
            'requests.Owner as Owner',
            'requests.Description as Description',
            'requests.IP',
            DB::raw("SUBSTRING(requests.GroupId, LOCATE('_', requests.GroupId, LOCATE('_', requests.GroupId) + 1) + 1) AS SerialNumber")


        ])
            ->leftJoin($this->pingback . '.requests', 'licenses.GroupId', '=', 'requests.GroupIdLicense')

            ->whereRaw('requests.LastPingTimeStamp = (select max(pb2.LastPingTimeStamp) from ' . $this->pingback . '.requests pb2 where licenses.GroupId = pb2.GroupIdLicense)')
            ->groupBy([
                'licenses.GroupId',
                'requests.AppTitle',
                'requests.AppVersion',
                'requests.Owner',
                'requests.Description',
                'requests.GroupId',
                'requests.IP'
            ]);



        return $query;
    }

}
