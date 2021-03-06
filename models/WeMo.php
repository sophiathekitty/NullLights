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
            'Type'=>"varchar(100)",
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
            'Field'=>"type",
            'Type'=>"varchar(10)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"unknown",
            'Extra'=>""
        ],[
            'Field'=>"subtype",
            'Type'=>"varchar(20)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"unknown",
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
            'Field'=>"lock_state",
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
    /**
     * @return WeMoLights|clsModel
     */
    private static function GetInstance(){
        if(is_null(WeMoLights::$sensors)) WeMoLights::$sensors = new WeMoLights();
        return WeMoLights::$sensors;
    }
    public static function AllLights(){
        $sensors = WeMoLights::GetInstance();
        return $sensors->LoadAllWhere(null,["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
    public static function MacAddress($mac_address){
        $sensors = WeMoLights::GetInstance();
        return $sensors->LoadWhere(['mac_address'=>$mac_address]);
    }
    public static function SaveWeMo(array $data, $remote_data = false){
        $sensors = WeMoLights::GetInstance();
        $data = $sensors->CleanData($data);
        if($data['state'] == "-1") unset($data['state']);
        if(isset($data['name'])){
            $data['type'] = WeMoLights::WeMoType($data);
            $data['subtype'] = WeMoLights::WeMoSubType($data);    
        }
        $wemo = WeMoLights::MacAddress($data['mac_address']);
        if(is_null($wemo)){
            return $sensors->Save($data);
        }
        return $sensors->Save($data,['mac_address'=>$data['mac_address']],$remote_data);
    }
    public static function WeMoType($wemo){
        if(strpos(strtolower($wemo['name']),"light") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"lamp") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"ambient") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"fan") > -1) return "fan";
        return "other";
    }
    public static function WeMoSubType($wemo){
        if(strpos(strtolower($wemo['name']),"lava") > -1) return "lava";
        if(strpos(strtolower($wemo['name']),"ambient") > -1) return "ambient";
        if(strpos(strtolower($wemo['name']),"mood") > -1) return "mood";
        if(strpos(strtolower($wemo['name']),"lamp") > -1) return "lamp";
        if(strpos(strtolower($wemo['name']),"inquisition") > -1) return "inquisition";
        if(strpos(strtolower($wemo['name']),"stars") > -1) return "stars";
        if(strpos(strtolower($wemo['name']),"window") > -1) return "window";
        return "other";
    }

    public static function RoomLights($room_id,$subtype = null){
        return WeMoLights::RoomWeMos($room_id,"light",$subtype);
    }
    public static function RoomWeMos($room_id,$type = null, $subtype = null){
        $sensors = WeMoLights::GetInstance();
        if(is_null($type) && is_null($subtype)) return $sensors->LoadAllWhere(['room_id'=>$room_id],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        if(is_null($subtype)) return $sensors->LoadAllWhere(['room_id'=>$room_id,'type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        return $sensors->LoadAllWhere(['room_id'=>$room_id,'type'=>$subtype,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
    public static function WeMos($type, $subtype = null){
        $sensors = WeMoLights::GetInstance();
        if(is_null($subtype)) return $sensors->LoadAllWhere(['type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        return $sensors->LoadAllWhere(['type'=>$subtype,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLights();
}
?>