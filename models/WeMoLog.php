<?php
/**
 * logs the state of wemos so i can track how long they've been on or off
 * and when they've been on or off recently
 */
class WeMoLogs extends clsModel {
    public $table_name = "WeMoLogs";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
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
    private static function GetInstance():WeMoLogs{
        if(is_null(WeMoLogs::$instance)) WeMoLogs::$instance = new WeMoLogs();
        return WeMoLogs::$instance;
    }
    /**
     * add a new log
     * @param string $mac_address the mac address of the wemo
     * @param int $state the wemo's current state
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function AddLog(string $mac_address, int $state){
        WeMoLogs::SaveLog(['mac_address'=>$mac_address,'state'=>$state]);
    }
    /**
     * save wemo log
     * @param array $data the wemo (log) data array
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLog(array $data){
        $instance = WeMoLogs::GetInstance();
        $instance->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('wemo_log_days',1)));
        return null;
        $data = $instance->CleanData($data);
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['state']);
        return $instance->Save($data);
    }
    /**
     * load logs for a wemo
     * @param string $mac_address the mac address of the wemo
     * @return array logs for the wemo
     */
    public static function MacAddress($mac_address){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadAllWhere(['mac_address'=>$mac_address],['created'=>'DESC']);
    }
    /**
     * gets recent logs 
     * @param string $mac_address the mac address of the wemo
     * @param int $seconds how many seconds back in time to go
     * @return array logs for the wemo
     */
    public static function Recent($mac_address,$seconds){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadWhereFieldAfter(['mac_address'=>$mac_address],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    /**
     * get the logs for a wemo from a specific hour
     * @param string $mac_address the mac address of the wemo
     * @param int $hour the hour to filter by 0-24
     * @return array logs for the wemo
     */
    public static function Hour($mac_address,$hour){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadFieldHourWhere("`mac_address` = '$mac_address'",'created',$hour);
    }
    /**
     * get the logs where sql and filter by hour
     * @param string $where where sql stream
     * @param int $hour the hour to filter by 0-24
     * @return array logs for the wemo
     */
    public static function HourWhere($where,$hour){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadFieldHourWhere($where,'created',$hour);
    }
    /**
     * get logs where sql
     * @param string $where where sql stream
     * @return array logs for the wemo
     */
    public static function Where($where){
        $instance = WeMoLogs::GetInstance();
        return clsDB::$db_g->select("SELECT * FROM `".$instance->table_name."` WHERE $where;");
    }
    /**
     * get the last log with state 1
     * @param string $mac_address the mac address of the wemo
     * @return array most recent wemo with an on state
     */
    public static function LastOn($mac_address){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address,"state"=>1],['created'=>'DESC']);
    }
    /**
     * get the last log with state 0
     * @param string $mac_address the mac address of the wemo
     * @return array most recent wemo with an off state
     */
    public static function LastOff($mac_address){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address,"state"=>0],['created'=>'DESC']);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLogs();
}
?>