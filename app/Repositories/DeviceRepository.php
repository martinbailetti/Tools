<?php

namespace App\Repositories;

use App\Models\Hardwarelink\Ip;
use App\Models\Hardwarelink\License;
use App\Models\Hardwarelink\LicenseAnnotation;
use App\Models\Hardwarelink\LicenseHistory;
use Illuminate\Support\Facades\DB;

class DeviceRepository
{

    private $pingback = '';

    public function __construct()
    {
        $this->pingback = DB::connection('mysql_pingback')->getDatabaseName();
    }



    public function getSimpleDevicesQuery()
    {


        $query = License::query()->select([
            'l.GroupId',
            'l.MachineId',
            'l.Id',
            'l.Position',
            'l.Type',
            'l.TypeInfo',
            'l.ManufacturerSerialNumber',
            'l.RPI',
            DB::raw('MAX(l.LastDate) as LastDate')

        ])
            ->from('licenses as l')
            ->leftJoin($this->pingback . '.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (select max(pb2.LastPingTimeStamp) from ' . $this->pingback . '.requests pb2 where l.GroupId = pb2.GroupIdLicense)')
            ->groupBy([
                'l.GroupId',
                'l.MachineId',
                'l.Id',
                'l.Position',
                'l.Type',
                'l.TypeInfo',
                'l.ManufacturerSerialNumber',
                'l.RPI'
            ]);



        return $query;
    }

    public function getSearchFilteredDevicesQuery($filters)
    {


        $query = $this->getDevicesQuery();
        if ($filters) {
            foreach ($filters as $filter) {


                if ($filter["id"] == 'search') {
                    $query->where(function ($query) use ($filter) {

                        $query->orWhere("l.ClientNumber", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.MachineSerialNumber", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.SpareOrder", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.Type", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.TypeInfo", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.ProductDefinition", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.GroupId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.MachineId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.Id", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.ManufacturerSerialNumber", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.RPI", "like", "%" . $filter["value"] . "%");
                        $query->orWhere(DB::raw("SUBSTRING(pb.GroupId, LOCATE('_', pb.GroupId, LOCATE('_', pb.GroupId) + 1) + 1)"), "like", "%" . $filter["value"] . "%");
                    });
                } else if ($filter["id"] == 'Type_autofill_multi') {
                    $query->whereIn("Type", $filter["value"]);
                } else if ($filter["id"] == 'TypeInfo_autofill_multi') {
                    $query->whereIn("TypeInfo", $filter["value"]);
                } else if ($filter["id"] == 'Id_autofill_multi') {
                    $query->whereIn("l.Id", $filter["value"]);
                } else if ($filter["id"] == 'ProductDefinition_autofill_multi') {
                    $query->whereIn("l.ProductDefinition", $filter["value"]);
                } else if ($filter["id"] == 'RPI_autofill_multi') {
                    $query->whereIn("l.RPI", $filter["value"]);
                } else if ($filter["id"] == 'SpareOrder_autofill_multi') {
                    $query->whereIn("l.SpareOrder", $filter["value"]);
                } else if ($filter["id"] == 'GroupId_autofill_multi') {
                    $query->whereIn("l.GroupId", $filter["value"]);
                } else if ($filter["id"] == 'SerialNumber_autofill_multi') {
                    $query->whereIn(DB::raw("SUBSTRING(pb.GroupId, LOCATE('_', pb.GroupId, LOCATE('_', pb.GroupId) + 1) + 1)"), $filter["value"]);
                } else if ($filter["id"] == 'ManufacturerSerialNumber_autofill_multi') {
                    $query->whereIn("l.ManufacturerSerialNumber", $filter["value"]);
                } else if ($filter["id"] == 'MachineSerialNumber_autofill_multi') {
                    $query->whereIn("l.MachineSerialNumber", $filter["value"]);
                } else if ($filter["id"] == 'ClientNumber_autofill_multi') {
                    $query->whereIn("l.ClientNumber", $filter["value"]);
                } else if ($filter["id"] == 'Client_autofill_multi') {
                    $query->whereIn("l.Client", $filter["value"]);
                } else if ($filter["id"] == 'OnlyFactory') {
                    $ips = Ip::select([
                        'IP'
                    ])->get();



                    $ipsArray = $ips->pluck('IP')->toArray();

                    if ($filter["value"] == 1) {

                        $query->whereIn("pb.IP", $ipsArray);
                    } else {

                        $query->whereNotIn("pb.IP", $ipsArray);
                    }

                } else if ($filter["id"] == 'GroupId') {
                    $query->where("l.GroupId", $filter["value"]);
                } else if ($filter["id"] == 'MachineId') {
                    $query->where("l.MachineId", $filter["value"]);
                }
            }
        }


        return $query;
    }

    public function getDevicesQuery()
    {
        $ips = Ip::select([
            'IP'
        ])->get();

        $ipsArray = $ips->pluck('IP')->toArray();


        $query = License::query()->select([
            'l.GroupId',
            'l.MachineId',
            'l.Id',
            'l.IsSpare',
            'l.SpareOrder',
            'l.ClientNumber',
            'l.Client',
            'l.ManufacturerSerialNumber',
            'l.QRCode',
            'l.Position',
            'l.Type',
            'l.TypeInfo',
            'l.WhyActive',
            'l.WhyActiveString',
            'l.ActionRequest',
            'l.SyncActionRequest',
            'l.LastLifeDate',
          
            'l.OfflineActived',
            'l.OfflineActivedDate',
            'l.OfflineActivedKey',
            'l.OfflineActiveRequest',
            'l.LastActivatedDate',
            'l.FirstDate',
            'l.Expiration',
            'l.LastMachineDate',
            'l.LastIP',
            'l.FactoryDevice',
            'l.RPI',
            'l.ProductDefinition',
            'l.CustomizedQR',
            'l.MachineSerialNumber',
            'pb.AppTitle',
            'pb.AppVersion',
            'pb.AppDateTime',
            'pb.LastPingTimeStamp',
            'pb.Owner',
            'pb.Description',
            DB::raw("(select name from factory_clients where id=l.Client) as FactoryClientName"),
            DB::raw("SUBSTRING(pb.GroupId, LOCATE('_', pb.GroupId, LOCATE('_', pb.GroupId) + 1) + 1) AS SerialNumber"),
            'pb.GroupId as pbgid',
            'pb.IP',
            DB::raw('MAX(l.LastDate) as LastDate'),
            'l.Active',
            DB::raw('CASE WHEN l.Active = 1 AND l.WhyActive NOT IN (257, 263) THEN 1 ELSE 0 END as Validated'),
            DB::raw('CASE WHEN pb.IP IN (' . implode(',', array_map(function ($ip) {
                return "'" . $ip . "'";
            }, $ipsArray)) . ') THEN 1 ELSE 0 END as FactoryIP')
        ])
            ->from('licenses as l')
            ->leftJoin($this->pingback . '.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (select max(pb2.LastPingTimeStamp) from ' . $this->pingback . '.requests pb2 where l.GroupId = pb2.GroupIdLicense)')
            ->groupBy([
                'l.GroupId',
                'l.MachineId',
                'l.Id',
                'l.ManufacturerSerialNumber',
                'l.QRCode',
                'l.Position',
                'l.Type',
                'l.TypeInfo',
                'l.WhyActive',
                'l.WhyActiveString',
                'l.ActionRequest',
                'l.SyncActionRequest',
                'l.LastLifeDate',
                'l.OfflineActived',
                'l.OfflineActivedDate',
                'l.OfflineActivedKey',
                'l.OfflineActiveRequest',
                'l.LastActivatedDate',
                'l.IsSpare',
                'l.SpareOrder',
                'l.ClientNumber',
                'l.Client',
                'l.FirstDate',
                'l.Expiration',
                'l.LastMachineDate',
                'l.LastIP',
                'pb.AppTitle',
                'pb.AppVersion',
                'pb.AppDateTime',
                'pb.LastPingTimeStamp',
                'pb.Owner',
                'pb.Description',
                'pb.GroupId',
                'pb.IP',
                'l.FactoryDevice',
                'l.RPI',
                'l.ProductDefinition',
                'l.MachineSerialNumber',
                'l.CustomizedQR',
                'l.Active'
            ]);



        return $query;
    }

    public function getFilteredDevicesQuery($filters, $GroupId = "", $MachineId = "")
    {


        $query = $this->getDevicesQuery();
        if (!empty($GroupId) && !empty($MachineId)) {
            $query->where('l.GroupId', $GroupId);
            $query->where('l.MachineId', $MachineId);
        }
        if ($filters) {
            foreach ($filters as $filter) {


                if ($filter["id"] == 'search') {
                    $query->where(function ($query) use ($filter) {

                        $query->orWhere("l.GroupId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.MachineId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.Id", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.ManufacturerSerialNumber", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.RPI", "like", "%" . $filter["value"] . "%");
                        $query->orWhere(DB::raw("SUBSTRING(pb.GroupId, LOCATE('_', pb.GroupId, LOCATE('_', pb.GroupId) + 1) + 1)"), "like", "%" . $filter["value"] . "%");
                    });
                } else if ($filter["id"] == 'Type_autofill_multi') {
                    $query->whereIn("Type", $filter["value"]);
                } else if ($filter["id"] == 'TypeInfo_autofill_multi') {
                    $query->whereIn("TypeInfo", $filter["value"]);
                } else if ($filter["id"] == 'Id') {
                    $query->where("l.Id", $filter["value"]);
                }
            }
        }


        return $query;
    }

    public function getDevicesAllQuery($filters)
    {

        $ips = Ip::select([
            'IP'
        ])->get();

        $ipsArray = $ips->pluck('IP')->toArray();

        $query = $this->getDevicesQuery();

        if ($filters) {
            foreach ($filters as $filter) {


                if ($filter["id"] == 'search') {
                    $query->where("l.Id", "like", "%" . $filter["value"] . "%");
                } else if ($filter["id"] == 'FactoryIP') {
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

    public function getFilteredDevicesListQuery($filters)
    {

        $query = $this->getDevicesQuery();

        if ($filters) {
            foreach ($filters as $filter) {


                if ($filter["id"] == 'search') {
                    $query->where(function ($query) use ($filter) {

                        $query->orWhere("l.GroupId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.MachineId", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.Id", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.ManufacturerSerialNumber", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("l.RPI", "like", "%" . $filter["value"] . "%");
                        $query->orWhere("pb.GroupId", "like", "%" . $filter["value"] . "%");
                    });
                } else if ($filter["id"] == 'GroupId') {
                    $query->where("l.GroupId", $filter["value"]);
                } else if ($filter["id"] == 'SerialNumber') {
                    $query->where("pb.GroupId", "like", "%" . $filter["value"] . "%");
                } else if ($filter["id"] == 'Type_autofill_multi') {
                    $query->whereIn("l.Type", $filter["value"]);
                } else if ($filter["id"] == 'TypeInfo_autofill_multi') {
                    $query->whereIn("l.TypeInfo", $filter["value"]);
                }
            }
        }


        return $query;
    }


    public function getDeviceHistory($id)
    {

        $query = LicenseHistory::where('Id', $id)->orderBy("InsertionTimestamp", "desc")->orderBy("AutoInc", "desc");
        return $query;
    }

    public function updateDevice($params)
    {
        $GroupId = $params["GroupId"] ?? null;
        $MachineId = $params["MachineId"] ?? null;
        $Id = $params["Id"] ?? null;


        if ($GroupId == null || $MachineId == null || $Id == null) {

            return ['success' => false, 'message' => "undefined_error"];
        }

        try {

            $result = License::where('Id', $Id)
                ->where('MachineId', $MachineId)
                ->where('GroupId', $GroupId)
                ->first();

            if ($result instanceof License) {

                $GroupId = $params["GroupId"] ?? null;
                $MachineId = $params["MachineId"] ?? null;
                $Id = $params["Id"] ?? null;

                $newValues = [];


                if (isset($params["ManufacturerSerialNumber"])) {
                    $newValues["ManufacturerSerialNumber"] = $params["ManufacturerSerialNumber"];
                }
                if (isset($params["RPI"])) {
                    $newValues["RPI"] = $params["RPI"];
                }


                License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)->update($newValues);

                $result = License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)
                    ->first();


                LicenseAnnotation::where('group_id', $GroupId)
                    ->where('machine_id', $MachineId)
                    ->where('device_id', $Id)->delete();
                if (isset($params["Annotation"]) && !empty($params["Annotation"])) {
                    LicenseAnnotation::create([
                        'group_id' => $GroupId,
                        'machine_id' => $MachineId,
                        'device_id' => $Id,
                        'text' => $params["Annotation"]
                    ]);

                }
                return ['success' => true, 'result' => $result];
            } else {
                return ['success' => false, 'message' => "not_found"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function validateDevice($GroupId, $MachineId, $Id)
    {

        try {

            $result = License::where('Id', $Id)
                ->where('MachineId', $MachineId)
                ->where('GroupId', $GroupId)
                ->first();

            if ($result instanceof License) {

                License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)->update(['ActionRequest' => 2]);

                $result = License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)
                    ->first();
                return ['success' => true, 'result' => $result];
            } else {
                return ['success' => false, 'message' => "not_found"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function invalidateDevice($GroupId, $MachineId, $Id)
    {

        try {

            $result = License::where('Id', $Id)
                ->where('MachineId', $MachineId)
                ->where('GroupId', $GroupId)
                ->first();

            if ($result instanceof License) {

                License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)->update(['ActionRequest' => 1]);

                $result = License::where('Id', $Id)
                    ->where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)
                    ->first();
                return ['success' => true, 'result' => $result];
            } else {
                return ['success' => false, 'message' => "not_found"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    public function getColumnValues($search, $column, $GroupId = "", $MachineId = "")
    {
        $query = License::select([
            $column
        ])->distinct()
            ->orderBy($column);


        if ($GroupId != "") {
            $query->where('GroupId', $GroupId);
        }
        if ($MachineId != "") {
            $query->where('MachineId', $MachineId);
        }
        if ($search) {
            $query->where($column, 'like', '%' . $search . '%');
        }


        return $query;
    }
}
