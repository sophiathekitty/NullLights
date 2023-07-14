<?php
define('TuyaLightsPlugin',true);
/**
 * model for storing Tuya light data
 */
class TuyaLights extends clsModel {
    public $table_name = "TuyaLights";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"varchar(32)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"url",
            'Type'=>"varchar(20)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"local_key",
            'Type'=>"varchar(32)",
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
            'Field'=>"light_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"product_name",
            'Type'=>"varchar(50)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"unknown",
            'Extra'=>""
        ],[
            'Field'=>"product_type",
            'Type'=>"varchar(10)",
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
            'Field'=>"percent",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1",
            'Extra'=>""
        ],[
            'Field'=>"color",
            'Type'=>"varchar(30)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"229,224,111",
            'Extra'=>""
        ],[
            'Field'=>"target_percent",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"-1",
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
    private static $instance = null;
    /**
     * @return TuyaLights|clsModel
     */
    private static function GetInstance(){
        if(is_null(TuyaLights::$instance)) TuyaLights::$instance = new TuyaLights();
        return TuyaLights::$instance;
    }
    /**
     * load all TuyaDevices
     * sorted by type and then subtype
     * @return array all of the Tuya lights
     */
    public static function AllLights(){
        $instance = TuyaLights::GetInstance();
        return $instance->LoadAllWhere(null,["room_id"=>"ASC"]);
    }
    /**
     * load a Tuya by its id
     * @param string $id the mac address of the Tuya
     * @return array Tuya data array
     */
    public static function DeviceID($id){
        $instance = TuyaLights::GetInstance();
        return $instance->LoadWhere(['id'=>$id]);
    }
    /**
     * load all TuyaDevices for a light group
     * @param string $light_id the id of the light group
     * @return array Tuya data array
     */
    public static function LightGroup($light_id){
        $instance = TuyaLights::GetInstance();
        return $instance->LoadAllWhere(['light_id'=>$light_id]);
    }
    /**
     * save the Tuya
     * @param array $data the Tuya data object
     * @param bool $remote_data is this being synced from another device (make sure we're not overriding fresher local data)
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveTuya(array $data, $remote_data = false){
        $instance = TuyaLights::GetInstance();
        $data = $instance->CleanData($data);
        if($data['state'] == "-1") unset($data['state']);
        if(isset($data['product_name'])) $data['product_type'] = TuyaLights::ProductType($data);
        $Tuya = TuyaLights::DeviceID($data['id']);
        if(is_null($Tuya)){
            if(!isset($data['url'])) $data['url'] = "";
            return $instance->Save($data);
        }
        return $instance->Save($data,['id'=>$data['id']],$remote_data);
    }
    /**
     * figure out what type of RoomLight this is based on keywords in the name
     * @param array $RoomLight the RoomLight data array
     * @return string light, fan, other
     */
    public static function ProductType($tuya){
        if(strpos(strtolower($tuya['product_name']),"bulb") > -1) return "bulb";
        return "outlet";
    }

    /**
     * load all the Tuya lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of Tuya data arrays
     */
    public static function RoomLights($room_id,$subtype = null){
        //Debug::Log("TuyaLights::RoomLights",$room_id,$subtype);
        return TuyaLights::RoomTuyaDevices($room_id,"light",$subtype);
    }
    /**
     * load all the Tuya lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of Tuya data arrays
     */
    public static function UnGrouped(){
        //Debug::Log("TuyaLights::RoomLights",$room_id,$subtype);
        $instance = TuyaLights::GetInstance();
        return $instance->LoadAllWhere(['light_id'=>0]);
    }
    /**
     * load all of the TuyaDevices in a room
     * @param int $room_id the room's id
     * @param string|null $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of Tuya data arrays
     */
    public static function RoomTuyaDevices($room_id){
        //Debug::Log("TuyaLights::RoomTuyaDevices",$room_id,$type,$subtype);
        $instance = TuyaLights::GetInstance();
        return $instance->LoadAllWhere(['room_id'=>$room_id],["room_id"=>"ASC"]);
    }
    /**
     * load all of type (and subtype)
     * @param string $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of Tuya data arrays
     */
    public static function TuyaDevices(){
        $instance = TuyaLights::GetInstance();
        return $instance->LoadAllWhere(null,["room_id"=>"ASC"]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new TuyaLights();
}
?>