<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Hardwarelink\License;
use App\Repositories\DeviceRepository;
use App\Repositories\GroupRepository;
use App\Repositories\MachineRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminHomeController extends Controller
{
    protected $deviceRepository;
    protected $groupRepository;
    protected $machineRepository;

    public function __construct(DeviceRepository $deviceRepository, MachineRepository $machineRepository, GroupRepository $groupRepository)
    {
        $this->deviceRepository = $deviceRepository;
        $this->machineRepository = $machineRepository;
        $this->groupRepository = $groupRepository;
    }


    public function getActivatingDevices()
    {

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (SELECT MAX(pb2.LastPingTimeStamp) FROM pingback.requests pb2 WHERE l.GroupId = pb2.GroupIdLicense)')
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')->from('hardwarelink.ips')->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->whereRaw('(SELECT COUNT(Id) FROM hardwarelink.licenses WHERE l.Id = Id AND Quarantine = 1) = 0')
            ->where('ActionRequest', 2)
            ->where('SyncActionRequest', '<>', 1)
            ->whereRaw('TIMESTAMPDIFF(HOUR, l.LastLifeDate, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId)) < ' . config("hardwarelink.max_disconnected_device_in_connected_machine_hours"))
            ->whereRaw('TIMESTAMPDIFF(SECOND, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId), CURRENT_TIMESTAMP()) < ' . config("hardwarelink.max_disconnected_machine_days") . ' * 24 * 60 * 60')
            ->orderBy('GroupId')
            ->orderBy('MachineId')
            ->orderBy('Id')
            ->select('l.Id as Id', 'l.MachineId as MachineId', 'l.GroupId as GroupId', 'pb.AppTitle as AppTitle', 'l.FirstDate')
            ->get();

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function getExpirationNoDate()
    {

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (SELECT MAX(pb2.LastPingTimeStamp) FROM pingback.requests pb2 WHERE l.GroupId = pb2.GroupIdLicense)')
            ->where('WhyActive', 39321)
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')->from('hardwarelink.ips')->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->whereRaw('(SELECT COUNT(Id) FROM hardwarelink.licenses WHERE l.Id = Id AND Quarantine = 1) = 0')
            ->whereRaw('TIMESTAMPDIFF(HOUR, l.LastLifeDate, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId)) < ' . config("hardwarelink.max_disconnected_device_in_connected_machine_hours"))
            ->whereRaw('TIMESTAMPDIFF(SECOND, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId), CURRENT_TIMESTAMP()) < ' . config("hardwarelink.max_disconnected_machine_days") . ' * 24 * 60 * 60')
            ->whereNotIn('ActionRequest', [1, 2])
            ->where('SyncActionRequest', '<>', 1)
            ->whereRaw('(SELECT COUNT(*) FROM hardwarelink.quarantine_machines q WHERE q.GroupId = l.GroupId AND q.MachineId = l.MachineId) = 0')
            ->orderBy('GroupId')
            ->orderBy('MachineId')
            ->orderBy('Id')
            ->select('l.Id as Id', 'l.MachineId as MachineId', 'l.GroupId as GroupId', 'pb.AppTitle as AppTitle', 'l.FirstDate')
            ->get();

        return response()->json(['success' => true, 'result' => $result]);
    }

    public function getExpiration()
    {

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (SELECT MAX(pb2.LastPingTimeStamp) FROM pingback.requests pb2 WHERE l.GroupId = pb2.GroupIdLicense)')
            ->where(function ($query) {
                $query->whereIn('WhyActive', [257, 263])
                    ->orWhere('Active', 0);
            })
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')->from('hardwarelink.ips')->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->whereRaw('(SELECT COUNT(Id) FROM hardwarelink.licenses WHERE l.Id = Id AND Quarantine = 1) = 0')
            ->whereRaw('TIMESTAMPDIFF(HOUR, l.LastLifeDate, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId)) < ' . config("hardwarelink.max_disconnected_device_in_connected_machine_hours"))
            ->whereRaw('TIMESTAMPDIFF(SECOND, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId), CURRENT_TIMESTAMP()) < ' . config("hardwarelink.max_disconnected_machine_days") . ' * 24 * 60 * 60')
            ->whereNotIn('ActionRequest', [1, 2])
            ->where('SyncActionRequest', '<>', 1)
            ->whereRaw('(SELECT COUNT(*) FROM hardwarelink.quarantine_machines q WHERE q.GroupId = l.GroupId AND q.MachineId = l.MachineId) = 0')
            ->orderBy('GroupId')
            ->orderBy('MachineId')
            ->orderBy('Id')
            ->select('l.Id as Id', 'l.MachineId as MachineId', 'l.GroupId as GroupId', 'pb.AppTitle as AppTitle', 'l.FirstDate')
            ->get();

        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getQuarantine()
    {

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->select([
                'l.Id as Id',
                'l.MachineId as MachineId',
                'l.GroupId as GroupId',
                'pb.AppTitle as AppTitle',
                'l.QuarantineDate'
            ])
            ->whereRaw('pb.LastPingTimeStamp = (
            SELECT MAX(pb2.LastPingTimeStamp)
            FROM pingback.requests pb2
            WHERE l.GroupId = pb2.GroupIdLicense
        )')
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')
                    ->from('hardwarelink.ips')
                    ->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->where('Quarantine', 1)
            ->orderBy('GroupId')
            ->orderBy('MachineId')
            ->orderBy('Id')
            ->distinct()
            ->get();


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getMachineQuarantine()
    {

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (SELECT MAX(pb2.LastPingTimeStamp) FROM pingback.requests pb2 WHERE l.GroupId = pb2.GroupIdLicense)')
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')->from('hardwarelink.ips')->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->whereRaw('(SELECT COUNT(*) FROM hardwarelink.quarantine_machines WHERE GroupId = l.GroupId AND MachineId = l.MachineId) > 0')
            ->select('l.MachineId as MachineId', 'l.GroupId as GroupId', 'pb.AppTitle as AppTitle')
            ->selectRaw('(SELECT MAX(created_at) FROM hardwarelink.quarantine_machines WHERE GroupId = l.GroupId AND MachineId = l.MachineId) as QuarantineDate')
            ->distinct()
            ->orderBy('QuarantineDate', 'desc')
            ->orderBy('GroupId', 'asc')
            ->orderBy('MachineId', 'asc')
            ->get();


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getPendingExpiration(Request $request)
    {

        $days = $request->input('days', 10);

        $result = DB::table('hardwarelink.licenses as l')
            ->join('pingback.requests as pb', 'l.GroupId', '=', 'pb.GroupIdLicense')
            ->whereRaw('pb.LastPingTimeStamp = (SELECT MAX(pb2.LastPingTimeStamp) FROM pingback.requests pb2 WHERE l.GroupId = pb2.GroupIdLicense)')
            ->whereNotIn('pb.IP', function ($query) {
                $query->select('ip')->from('hardwarelink.ips')->where('factory', 1);
            })
            ->where('FactoryDevice', 0)
            ->whereRaw('(SELECT COUNT(Id) FROM hardwarelink.licenses WHERE l.Id = Id AND Quarantine = 1) = 0')
            ->whereIn('WhyActive', [257, 263])
            ->whereRaw('TIMESTAMPDIFF(HOUR, l.LastLifeDate, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId)) < ' . config("hardwarelink.max_disconnected_device_in_connected_machine_hours"))
            ->whereRaw('TIMESTAMPDIFF(SECOND, (SELECT MAX(LastLifeDate) FROM hardwarelink.licenses WHERE GroupId = l.GroupId AND MachineId = l.MachineId), CURRENT_TIMESTAMP()) < ' . config("hardwarelink.max_disconnected_machine_days") . ' * 24 * 60 * 60')
            ->whereRaw('TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP(), Expiration) < ' . $days . ' * 24 * 60 * 60')
            ->whereNotIn('ActionRequest', [1, 2])
            ->where('SyncActionRequest', '<>', 1)
            ->whereRaw('(SELECT COUNT(*) FROM hardwarelink.quarantine_machines q WHERE q.GroupId = l.GroupId AND q.MachineId = l.MachineId) = 0')
            ->orderBy('GroupId')
            ->orderBy('MachineId')
            ->orderBy('Id')
            ->select('l.Id as Id', 'l.MachineId as MachineId', 'l.GroupId as GroupId', 'pb.AppTitle as AppTitle', 'l.FirstDate')
            ->get();


        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getGroupsCount()
    {

        $result = $this->groupRepository->getGroupsSimpleQuery()
            ->selectRaw('COUNT(*) as count')
            ->first();


        $total = License::distinct('GroupId')->count();

        return response()->json(['success' => true, 'result' => $total]);
    }
    public function getMachinesCount()
    {
        $total = License::distinct('MachineId')->count(DB::raw('DISTINCT MachineId'));
        return response()->json(['success' => true, 'result' => $total]);
    }
    public function getDevicesCount()
    {

        $total = License::count();


        return response()->json(['success' => true, 'result' => $total]);
    }
    public function getNewGroups()
    {

        $result = DB::select("SELECT distinct l.MachineId AS MachineId, l.GroupId AS GroupId, pb.AppTitle AS AppTitle,
        (select count(GroupId) from hardwarelink.licenses where GroupId=l.GroupId and New>0) as isnew, 
        (select count(GroupId) from hardwarelink.licenses where GroupId=l.GroupId and New=0) as isnotnew 
        FROM (hardwarelink.licenses l
        JOIN pingback.requests pb ON((l.GroupId = pb.GroupIdLicense)))
        WHERE (pb.LastPingTimeStamp = (
        SELECT MAX(pb2.LastPingTimeStamp)
        FROM pingback.requests pb2
        WHERE (l.GroupId = pb2.GroupIdLicense)))
        having isnew>0 and isnotnew=0 order by GroupId");



        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getNewMachines()
    {
        $result = DB::select("SELECT distinct l.MachineId AS MachineId, l.GroupId AS GroupId, pb.AppTitle AS AppTitle,
                (select count(GroupId) from hardwarelink.licenses where GroupId=l.GroupId and MachineId=l.MachineId and New>0) as isnew, 
                (select count(GroupId) from hardwarelink.licenses where GroupId=l.GroupId and MachineId=l.MachineId and New=0) as isnotnew 
                FROM (hardwarelink.licenses l
                JOIN pingback.requests pb ON((l.GroupId = pb.GroupIdLicense)))
                WHERE (pb.LastPingTimeStamp = (
                SELECT MAX(pb2.LastPingTimeStamp)
                FROM pingback.requests pb2
                WHERE (l.GroupId = pb2.GroupIdLicense)))
                having isnew>0 and isnotnew=0 order by GroupId, MachineId");
        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getNewDevices()
    {

        $result = DB::select("SELECT l.Id AS Id,l.MachineId AS MachineId,l.GroupId AS GroupId,pb.AppTitle AS AppTitle
        FROM (hardwarelink.licenses l
        JOIN pingback.requests pb ON((l.GroupId = pb.GroupIdLicense)))
        WHERE (pb.LastPingTimeStamp = (
        SELECT MAX(pb2.LastPingTimeStamp)
        FROM pingback.requests pb2
        WHERE (l.GroupId = pb2.GroupIdLicense)))
        and New>0  and pb.IP not in (select ip from hardwarelink.ips where factory=1)
        and FactoryDevice=0         
        and Quarantine=0
        order by GroupId, MachineId, Id");

        return response()->json(['success' => true, 'result' => $result]);
    }
    public function getNewExpirationDevices()
    {
    
        
        $result = DB::select("SELECT l.Id AS Id,l.MachineId AS MachineId,l.GroupId AS GroupId,pb.AppTitle AS AppTitle
        FROM (hardwarelink.licenses l
        JOIN pingback.requests pb ON((l.GroupId = pb.GroupIdLicense)))
        WHERE (pb.LastPingTimeStamp = (
        SELECT MAX(pb2.LastPingTimeStamp)
        FROM pingback.requests pb2
        WHERE (l.GroupId = pb2.GroupIdLicense)))
    
        and WhyActive in (257, 263) 
        and pb.IP not in (select ip from hardwarelink.ips where factory=1) 
        and FactoryDevice=0
        and Quarantine=0
        and  New>0
    and ActionRequest not in(1, 2)
            and SyncActionRequest<>1
        
        and TIMESTAMPDIFF(SECOND, l.LastLifeDate, (
            select max(LastLifeDate)  from hardwarelink.licenses 
            where GroupId=l.GroupId and MachineId=l.MachineId
            ) )<" . config("hardwarelink.max_disconnected_device_in_connected_machine_hours") . "
            
        and TIMESTAMPDIFF(SECOND, (
            select max(LastLifeDate)  from hardwarelink.licenses 
            where GroupId=l.GroupId and MachineId=l.MachineId
            ),CURRENT_TIMESTAMP() )<" . config("hardwarelink.max_disconnected_machine_days") . "*24*60*60
            
        order by GroupId, MachineId, Id");

        return response()->json(['success' => true, 'result' => $result]);
    }
}
