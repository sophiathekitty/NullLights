<?php

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
    private static $sensors = null;
    private static function GetInstance(){
        if(is_null(WeMoLogs::$sensors)) WeMoLogs::$sensors = new WeMoLogs();
        return WeMoLogs::$sensors;
    }
    public static function AddLog(string $mac_address, int $state){
        WeMoLogs::SaveLog(['mac_address'=>$mac_address,'state'=>$state]);
    }
    public static function SaveLog(array $data){
        $sensors = WeMoLogs::GetInstance();
        $sensors->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('wemo_log_days',2)));
        $data = $sensors->CleanData($data);
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['state']);
        return $sensors->Save($data);
    }
    public static function MacAddress($mac_address){
        $sensors = WeMoLogs::GetInstance();
        return $sensors->LoadWhere(['mac_address'=>$mac_address]);
    }
    public static function Recent($mac_address,$seconds){
        $sensors = WeMoLogs::GetInstance();
        return $sensors->LoadWhereFieldAfter(['mac_address'=>$mac_address],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    public static function Hour($mac_address,$hour){
        $sensors = WeMoLogs::GetInstance();
        return $sensors->LoadFieldHourWhere("`mac_address` = '$mac_address'",'created',$hour);
    }
    public static function HourWhere($where,$hour){
        $sensors = WeMoLogs::GetInstance();
        return $sensors->LoadFieldHourWhere($where,'created',$hour);
    }
    public static function LastOn($mac_address){
        $sensors = WeMoLogs::GetInstance();
        $sensors = new clsModel();
        return $sensors->LoadWhere(['mac_address'=>$mac_address,"state"=>1],['created'=>'DESC']);
    }
    public static function LastOff($mac_address){
        $sensors = WeMoLogs::GetInstance();
        $sensors = new clsModel();
        return $sensors->LoadWhere(['mac_address'=>$mac_address,"state"=>0],['created'=>'DESC']);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLogs();
}
?>