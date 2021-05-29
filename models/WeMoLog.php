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
            'Type'=>"varchar(100)",
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
        if(is_null(WeMoLogs::$sensors)) WeMoLogs::$sensors = new WeMoLogs();
        return WeMoLogs::$sensors;
    }
    public static function AddLog($mac_address,$state){
        WeMoLogs::SaveLog(['mac_address'=>$mac_address,'state'=>$state]);
    }
    public static function SaveLog($data){
        $sensors = WeMoLogs::GetInstance();
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['state']);
        return $sensors->Save($data);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLogs();
}
?>