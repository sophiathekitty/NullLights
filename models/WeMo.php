<?php
define('WeMoLightsPlugin',true);
/**
 * model for storing wemo light data
 */
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
    private static $instance = null;
    /**
     * @return WeMoLights|clsModel
     */
    private static function GetInstance(){
        if(is_null(WeMoLights::$instance)) WeMoLights::$instance = new WeMoLights();
        return WeMoLights::$instance;
    }
    /**
     * load all wemos
     * sorted by type and then subtype
     * @return array all of the wemo lights
     */
    public static function AllLights(){
        $instance = WeMoLights::GetInstance();
        return $instance->LoadAllWhere(null,["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
    /**
     * load a wemo by its mac_address
     * @param string $mac_address the mac address of the wemo
     * @return array wemo data array
     */
    public static function MacAddress($mac_address){
        $instance = WeMoLights::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address]);
    }
    /**
     * save the wemo
     * @param array $data the wemo data object
     * @param bool $remote_data is this being synced from another device (make sure we're not overriding fresher local data)
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveWeMo(array $data, $remote_data = false){
        $instance = WeMoLights::GetInstance();
        $data = $instance->CleanData($data);
        if($data['state'] == "-1") unset($data['state']);
        if(isset($data['name'])){
            $data['type'] = WeMoLights::WeMoType($data);
            $data['subtype'] = WeMoLights::WeMoSubType($data);    
        }
        $wemo = WeMoLights::MacAddress($data['mac_address']);
        if(is_null($wemo)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['mac_address'=>$data['mac_address']],$remote_data);
    }
    /**
     * figure out what type of wemo this is based on keywords in the name
     * @param array $wemo the wemo data array
     * @return string light, fan, other
     */
    public static function WeMoType($wemo){
        if(strpos(strtolower($wemo['name']),"light") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"lamp") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"ambient") > -1) return "light";
        if(strpos(strtolower($wemo['name']),"fan") > -1) return "fan";
        return "other";
    }
    /**
     * figure out the subtype of wemo based on name
     * @param array $wemo the wemo data array
     * @return string lava, ambient, mood, lamp, inquisition, stars, window, other
     */
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
    /**
     * load all the wemo lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of wemo data arrays
     */
    public static function RoomLights($room_id,$subtype = null){
        return WeMoLights::RoomWeMos($room_id,"light",$subtype);
    }
    /**
     * load all of the wemos in a room
     * @param int $room_id the room's id
     * @param string|null $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of wemo data arrays
     */
    public static function RoomWeMos($room_id,$type = null, $subtype = null){
        $instance = WeMoLights::GetInstance();
        if(is_null($type) && is_null($subtype)) return $instance->LoadAllWhere(['room_id'=>$room_id],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        if(is_null($subtype)) return $instance->LoadAllWhere(['room_id'=>$room_id,'type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        return $instance->LoadAllWhere(['room_id'=>$room_id,'type'=>$subtype,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
    /**
     * load all of type (and subtype)
     * @param string $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of wemo data arrays
     */
    public static function WeMos($type, $subtype = null){
        $instance = WeMoLights::GetInstance();
        if(is_null($subtype)) return $instance->LoadAllWhere(['type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        return $instance->LoadAllWhere(['type'=>$subtype,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoLights();
}
?>