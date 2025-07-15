<?php

namespace App\Repositories;

use App\Models\Hardwarelink\License;
use App\Models\Hardwarelink\LicensesMachineModel;
use App\Models\Hardwarelink\MachineModelDeviceType;
use Illuminate\Support\Facades\DB;

class FactoryMachineRepository
{

    private $pingback = '';

    public function __construct()
    {
        $this->pingback = DB::connection('mysql_pingback')->getDatabaseName();
    }



    public function getFactoryMachinesQuery($group_id)
    {
        $query = License::select([
            'licenses.MachineId',
            'licenses.GroupId',
            'machine_models.name as ModelName',
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and MachineId=licenses.MachineId and l.FactoryDispatched=1) as FactoryDispatched'),
            DB::raw('(SELECT COUNT(l.Id) FROM licenses l WHERE licenses.GroupId = l.GroupId and MachineId=licenses.MachineId and l.QRCode=1) as PrintedQR'),
            DB::raw("(select max(Position) from licenses as l where l.GroupId=licenses.GroupId and MachineId=licenses.MachineId) as Position"),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId) as Total"),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId and Active=1) as Active"),
            DB::raw("(select count(distinct Id) from licenses as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId and SyncActionRequest=1) as Deleting"),
            DB::raw("(SELECT MAX(LastPingTimeStamp) FROM " . $this->pingback . ".requests WHERE licenses.GroupId = GroupIdLicense) as LastPingTimeStamp"),
            DB::raw("(select ModelId from licenses_machine_models as l where GroupId=licenses.GroupId and MachineId=licenses.MachineId) as ModelId"),
        ])
            ->leftJoin('licenses_machine_models', function ($join) {
                $join->on('licenses.GroupId', '=', 'licenses_machine_models.GroupId')
                    ->on('licenses.MachineId', '=', 'licenses_machine_models.MachineId');
            })
            ->leftJoin('machine_models', 'licenses_machine_models.ModelId', '=', 'machine_models.id')
            ->where('licenses.GroupId', $group_id)

            ->groupBy('licenses.MachineId', 'licenses.GroupId', 'machine_models.name');


        return $query;
    }


    public function checkFactoryMachinesModelsQuery($GroupId, $MachineId, $ModelId)
    {

        $query = License::query();
        $query->select('Type as type', DB::raw('count(*) as quantity'));
        $query->where('GroupId', $GroupId);
        $query->where('MachineId', $MachineId);
        $query->groupBy('Type');
        $query->orderBy('Type', "asc");

        $devicesResult = $query->get();


        $modelResult = MachineModelDeviceType::select("DeviceTypeId as type", "quantity")
            ->where('MachineModelId', $ModelId)
            ->orderBy('DeviceTypeId', "asc")
            ->get();


        if ($devicesResult->count() !== $modelResult->count()) {
            return false;
        }
        foreach ($devicesResult as $index => $device) {
            $model = $modelResult[$index];

            if ($device->type !== $model->type || $device->quantity !== $model->quantity) {
                return false;
            }
        }

        return true;
    }


    public function getFactoryMachine($GroupId, $MachineQuery)
    {
        $query = $this->getFactoryMachinesQuery($GroupId);

        $query->where('licenses.MachineId', $MachineQuery);

        return $query->first();
    }


    public function updateMachineModel($params)
    {
        $GroupId = $params["GroupId"] ?? null;
        $MachineId = $params["MachineId"] ?? null;
        $ModelId = $params["ModelId"] ?? null;


        if ($GroupId == null || $MachineId == null) {

            return ['success' => false, 'message' => "undefined_error"];
        }

        try {

            $result = License::where('MachineId', $MachineId)
                ->where('GroupId', $GroupId)
                ->first();

            if ($result instanceof License) {




                $mm = LicensesMachineModel::where('MachineId', $MachineId)
                    ->where('GroupId', $GroupId)->delete();


                if ($params["ModelId"]) {

                    $new = LicensesMachineModel::create(['GroupId' => $GroupId, 'MachineId' => $MachineId, 'ModelId' => $params["ModelId"]]);

                    if ($new) {
                        return ['success' => true, 'result' => $new];
                    } else {

                        return ['success' => false, 'result' => $new];
                    }
                } else {

                    return ['success' => true, 'result' => $mm];
                }
            } else {
                return ['success' => false, 'message' => "not_found"];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
