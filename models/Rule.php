<?php
/**
 * Automation Rules that can in theory be synced between hub devices so i don't have to
 * make sure they all have the legacy scripts forever.
 * @todo figure out what this actually needs...
 */
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
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"type",
            'Type'=>"varchar(20)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"subtype",
            'Type'=>"varchar(20)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
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
    private static $instance = null;
    private static function GetInstance():AutomationRules{
        if(is_null(AutomationRules::$instance)) AutomationRules::$instance = new AutomationRules();
        return AutomationRules::$instance;
    }
    /**
     * get all of the rules
     * @return array all of the rules
     */
    public static function All(){
        $instance = AutomationRules::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * get all of the rules for a specific light
     */
    public static function MacAddress($mac_address){
        $instance = AutomationRules::GetInstance();
        return $instance->LoadAllWhere(['mac_address'=>$mac_address]);
    }
    /**
     * save a rule
     * @param array $data data array for the rule
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveRule(array $data){
        $instance = AutomationRules::GetInstance();
        $data = $instance->CleanData($data);
        $rule = null;
        if(isset($data['id'])) $rule = $instance->LoadWhere(['id'=>$data['id']]);
        if(is_null($rule)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['id'=>$data['id']]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new AutomationRules();
}
?>