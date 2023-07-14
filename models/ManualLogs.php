<?php
/**
 * for logging when we manually do stuff with the lights in theory this
 * should include any time somebody uses the app interface to toggle a light
 * and when a light's state changes when observing the lights. 
 * @todo needs static load functions
 */
class ManualLogs extends clsModel {
    public $table_name = "ManualLogs";
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
            'Field'=>"user_id",
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
    private static function GetInstance(){
        if(is_null(ManualLogs::$instance)) ManualLogs::$instance = new ManualLogs();
        return ManualLogs::$instance;
    }
    /**
     * save the log for a light. this will add the current user's id to the log
     * @param array $data the log data array (could be a wemo light data array)
     * @return array a save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLog($data){
        $session = UserSession::CleanSessionData();
        $data['user_id'] = $session['user_id'];
        $instance = ManualLogs::GetInstance();
        $instance->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('automation_log_days',5)));
        return;
        $data = $instance->CleanData($data);
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['state']);
        $res = $instance->Save($data);
        Services::Log("NullLights::WeMoSync::Observe","ManualLogs::SaveLog -- error? ".$res['error']);
        return $res;
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ManualLogs();
}
?>