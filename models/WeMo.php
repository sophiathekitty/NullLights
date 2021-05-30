<?php
define('WeMoLightsPlugin',true);
class WeMoLights extends clsModel {
    public $table_name = "WeMoLights";
    public $fields = [
        [
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"url",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"port",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"name",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"state",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"-1",
            'Extra'=>""
        ],[
            'Field'=>"target_state",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"-1",
            'Extra'=>""
        ],[
            'Field'=>"error",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"color",
            'Type'=>"varchar(30)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"229,224,111",
            'Extra'=>""
        ],[
            'Field'=>"modified",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>"on update current_timestamp()"
        ]
    ];
    private static $sensors = null;
    private static function GetInstance(){
        if(is_null(WeMoLights::$sensors)) WeMoLights::$sensors = new WeMoLights();
        return WeMoLights::$sensors;
    }
    public static function AllLights(){
        $sensors = WeMoLights::GetInstance();
        return $sensors->LoadAll();
    }
    public static function MacAddress($mac_address){
        $sensors = WeMoLights::GetInstance();
        return $sensors->LoadWhere(['mac_address'=>$mac_address]);
    }
    public static function SaveWeMo(array $data){
        $sensors = WeMoLights::GetInstance();
        $data = $sensors->CleanData($data);
        $wemo = WeMoLights::MacAddress($data['mac_address']);
        if($wemo){
            return $sensors->Save($data,['mac_address'=>$data['mac_address']]);
        }
        return $sensors->Save($data);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLights();
}
?>