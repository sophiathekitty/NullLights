<?php

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
    private static function GetInstance(){
        if(is_null(AutomationLogs::$sensors)) AutomationLogs::$sensors = new AutomationLogs();
        return AutomationLogs::$sensors;
    }
    public static function CreateAutomationLog($event,$details, $mac_address){
        AutomationLogs::SaveLog(['event'=>$event,"details"=>$details,'mac_address'=>$mac_address]);
    }
    public static function GetLastAutomationLightLog($mac_address,$event){
        $log = AutomationLogs::GetInstance();
        return $log->LoadWhere(['mac_address'=>$mac_address,'event'=>$event],['created'=>'DESC']);
    }
    public static function TimeSinceAutomaticLightEvent($wemo,$event){
        $event = AutomationLogs::GetLastAutomationLightLog($wemo['mac_address'],$event);
        return time() - strtotime($event['datetime']);
    }
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