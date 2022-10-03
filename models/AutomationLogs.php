<?php
/**
 * log when the automation stuff changes the state of a light
 */
class AutomationLogs extends clsModel {
    public $table_name = "AutomationLogs";
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
            'Field'=>"event",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"details",
            'Type'=>"varchar(200)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
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
    private static $sensors = null;
    /**
     * @return AutomationLogs|clsModel
     */
    private static function GetInstance():AutomationLogs{
        if(is_null(AutomationLogs::$sensors)) AutomationLogs::$sensors = new AutomationLogs();
        return AutomationLogs::$sensors;
    }
    /**
     * create an automation log
     * @param string $event the event name
     * @param string $details the details about the event (what conditions were true or false)
     * @param string $mac_address the light's mac address
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function CreateAutomationLog($event,$details, $mac_address){
        return AutomationLogs::SaveLog(['event'=>$event,"details"=>$details,'mac_address'=>$mac_address]);
    }
    /**
     * get the most recent automation event for a light
     * @param string $mac_address the light's mac address
     * @param string $event the event name
     * @return 
     */
    public static function GetLastAutomationLightLog($mac_address,$event){
        $log = AutomationLogs::GetInstance();
        return $log->LoadWhere(['mac_address'=>$mac_address,'event'=>$event],['created'=>'DESC']);
    }
    /**
     * calculate the time since the last automation event for a light
     * @param array $wemo light data array needed for mac address `$wemo['mac_address']`
     * @param string $event the event name
     * @return int time in seconds since last time this event happened
     */
    public static function TimeSinceAutomaticLightEvent($wemo,$event){
        $event = AutomationLogs::GetLastAutomationLightLog($wemo['mac_address'],$event);
        return time() - strtotime($event['datetime']);
    }
    /**
     * save a new log
     * @notes use `AutomationLogs::CreateAutomationLog($event,$details, $mac_address)`
     * @param array $data log data array 
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLog($data){
        $sensors = AutomationLogs::GetInstance();
        $sensors->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('automation_log_days',5)));
        $data = $sensors->CleanData($data);
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['event']);
        return $sensors->Save($data);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new AutomationLogs();
}
?>