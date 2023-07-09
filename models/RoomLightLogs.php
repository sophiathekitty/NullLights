<?php
/**
 * logs the state of room lights so i can track how long they've been on or off
 * and when they've been on or off recently
 */
class RoomLightLogs extends clsModel {
    public $table_name = "RoomLightLogs";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"state",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"error",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    private static function GetInstance():RoomLightLogs{
        if(is_null(RoomLightLogs::$instance)) RoomLightLogs::$instance = new RoomLightLogs();
        return RoomLightLogs::$instance;
    }
    /**
     * add a new log
     * @param string $id the light id of the room light
     * @param int $state the room light's current state
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function AddLog(string $id, int $state){
        RoomLightLogs::SaveLog(['id'=>$id,'state'=>$state]);
    }
    /**
     * save room light log
     * @param array $data the room light (log) data array
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLog(array $data){
        $instance = RoomLightLogs::GetInstance();
        $instance->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('room_light_log_days',1)));
        $data = $instance->CleanData($data);
        $data['created'] = date("Y-m-d H:i:s");
        $data = $instance->AddGUID($data,'id','created','state');
        //$data['guid'] = md5($data['id'].date("Y-m-d H:i:s").$data['state']);
        //Services::Log("LightGroups::SyncStates","guid: ".$data['guid']);
        //Services::Log("LightGroups::SyncStates","id: ".$data['id']);
        //Services::Log("LightGroups::SyncStates","state: ".$data['state']);
        return $instance->Save($data);
    }
    /**
     * load logs for a room light
     * @param string $id the light id of the room light
     * @return array logs for the room light
     */
    public static function LightId($id){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadAllWhere(['id'=>$id],['created'=>'DESC']);
    }
    /**
     * gets recent logs 
     * @param string $id the light id of the room light
     * @param int $seconds how many seconds back in time to go
     * @return array logs for the room light
     */
    public static function Recent($id,$seconds){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadWhereFieldAfter(['id'=>$id],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    /**
     * get the logs for a room light from a specific hour
     * @param string $id the light id of the room light
     * @param int $hour the hour to filter by 0-24
     * @return array logs for the room light
     */
    public static function Hour($id,$hour){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadFieldHourWhere("`id` = '$id'",'created',$hour);
    }
    /**
     * get the logs where sql and filter by hour
     * @param string $where where sql stream
     * @param int $hour the hour to filter by 0-24
     * @return array logs for the room light
     */
    public static function HourWhere($where,$hour){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadFieldHourWhere($where,'created',$hour);
    }
    /**
     * get logs where sql
     * @param string $where where sql stream
     * @return array logs for the room light
     */
    public static function Where($where){
        $instance = RoomLightLogs::GetInstance();
        return clsDB::$db_g->select("SELECT * FROM `".$instance->table_name."` WHERE $where;");
    }
    /**
     * get the last log with state 1
     * @param string $id the light id of the room light
     * @return array most recent room light with an on state
     */
    public static function LastOn($id){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadWhere(['id'=>$id,"state"=>1],['created'=>'DESC']);
    }
    /**
     * get the last log with state 0
     * @param string $id the light id of the room light
     * @return array most recent room light with an off state
     */
    public static function LastOff($id){
        $instance = RoomLightLogs::GetInstance();
        return $instance->LoadWhere(['id'=>$id,"state"=>0],['created'=>'DESC']);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomLightLogs();
}
?>