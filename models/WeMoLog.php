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
    private static $instance = null;
    private static function GetInstance():WeMoLogs{
        if(is_null(WeMoLogs::$instance)) WeMoLogs::$instance = new WeMoLogs();
        return WeMoLogs::$instance;
    }
    public static function AddLog(string $mac_address, int $state){
        WeMoLogs::SaveLog(['mac_address'=>$mac_address,'state'=>$state]);
    }
    public static function SaveLog(array $data){
        $instance = WeMoLogs::GetInstance();
        $instance->PruneField('created',DaysToSeconds(Settings::LoadSettingsVar('wemo_log_days',1)));
        $data = $instance->CleanData($data);
        $data['guid'] = md5($data['mac_address'].date("Y-m-d H:i:s").$data['state']);
        return $instance->Save($data);
    }
    public static function MacAddress($mac_address){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address]);
    }
    public static function Recent($mac_address,$seconds){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadWhereFieldAfter(['mac_address'=>$mac_address],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    public static function Hour($mac_address,$hour){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadFieldHourWhere("`mac_address` = '$mac_address'",'created',$hour);
    }
    public static function HourWhere($where,$hour){
        $instance = WeMoLogs::GetInstance();
        return $instance->LoadFieldHourWhere($where,'created',$hour);
    }
    public static function Where($where){
        $instance = WeMoLogs::GetInstance();
        return clsDB::$db_g->select("SELECT * FROM `".$instance->table_name."` WHERE $where;");
    }
    public static function LastOn($mac_address){
        $instance = WeMoLogs::GetInstance();
        $instance = new clsModel();
        return $instance->LoadWhere(['mac_address'=>$mac_address,"state"=>1],['created'=>'DESC']);
    }
    public static function LastOff($mac_address){
        $instance = WeMoLogs::GetInstance();
        $instance = new clsModel();
        return $instance->LoadWhere(['mac_address'=>$mac_address,"state"=>0],['created'=>'DESC']);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLogs();
}
?>