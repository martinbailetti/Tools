<?php

namespace App\Repositories;

use App\Models\Webrepo\MachineDef;

class WebrepoRepository
{


    public function getMachineData($GroupId)
    {

        $result = MachineDef::leftJoin('machine_structs', 'machine_structs.MachineId', '=', 'machine_defs.MachineId')
            ->where('machine_structs.DallasId', $GroupId)
            ->select('machine_defs.SerialNumber', 'machine_defs.Arcade', 'machine_defs.WebrepoUser', 'machine_defs.Location', 'machine_defs.LocationLevel1', 'machine_defs.LocationLevel2', 'machine_defs.LocationLevel3', 'machine_defs.Customer')
            ->first();


        return $result;
    }
}
