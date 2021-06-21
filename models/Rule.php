<?php
class AutomationRules extends clsModel {
    public $table_name = "AutomationRules";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"name",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"type",
            'Type'=>"varchar(20)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"subtype",
            'Type'=>"varchar(20)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"conditions",
            'Type'=>"varchar(255)",
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
            'Field'=>"enabled",
            'Type'=>"tinyint(1)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ]
    ];
    private static $sensors = null;
    private static function GetInstance(){
        if(is_null(AutomationRules::$sensors)) AutomationRules::$sensors = new AutomationRules();
        return AutomationRules::$sensors;
    }
    public static function All(){
        $sensors = AutomationRules::GetInstance();
        return $sensors->LoadAll();
    }
    public static function MacAddress($mac_address){
        $sensors = AutomationRules::GetInstance();
        return $sensors->LoadWhere(['mac_address'=>$mac_address]);
    }
    public static function SaveRule(array $data){
        $sensors = AutomationRules::GetInstance();
        $data = $sensors->CleanData($data);
        $wemo = AutomationRules::MacAddress($data['mac_address']);
        if(is_null($wemo)){
            return $sensors->Save($data);
        }
        return $sensors->Save($data,['mac_address'=>$data['mac_address']]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new AutomationRules();
}
?>