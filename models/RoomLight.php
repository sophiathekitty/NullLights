<?php
/**
 * model for storing wemo light data
 */
class RoomLightsGroup extends clsModel {
    public $table_name = "RoomLightsGroup";
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
            'Field'=>"room_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"mode",
            'Type'=>"varchar(10)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"auto",
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
     * @return RoomLightsGroup|clsModel
     */
    private static function GetInstance(){
        if(is_null(RoomLightsGroup::$instance)) RoomLightsGroup::$instance = new RoomLightsGroup();
        return RoomLightsGroup::$instance;
    }
    /**
     * load all lights
     * sorted by type and then subtype
     * @return array all of the wemo lights
     */
    public static function AllLights(){
        $instance = RoomLightsGroup::GetInstance();
        return $instance->LoadAllWhere(null,["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
    /**
     * load a wemo by its id
     * @param string $id the id of the light
     * @return array wemo data array
     */
    public static function LightId($id){
        $instance = RoomLightsGroup::GetInstance();
        return $instance->LoadWhere(['id'=>$id]);
    }
    /**
     * save the wemo
     * @param array $data the wemo data object
     * @param bool $remote_data is this being synced from another device (make sure we're not overriding fresher local data)
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLight(array $data, $remote_data = false){
        $instance = RoomLightsGroup::GetInstance();
        $data = $instance->CleanData($data);
        if($data['state'] == "-1") unset($data['state']);
        if(isset($data['name'])){
            $data['type'] = WeMoLights::WeMoType($data);
            $data['subtype'] = WeMoLights::WeMoSubType($data);    
        }
        $light = RoomLightsGroup::LightId($data['id']);
        if(is_null($light)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['mac_address'=>$data['mac_address']],$remote_data);
    }
    /**
     * load all the wemo lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of wemo data arrays
     */
    public static function RoomLightsGroupLight($room_id,$subtype = null){
        return RoomLightsGroup::RoomDevices($room_id,"light",$subtype);
    }
    /**
     * load all of the devices in a room
     * @param int $room_id the room's id
     * @param string|null $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of wemo data arrays
     */
    public static function RoomDevices($room_id,$type = null, $subtype = null){
        $instance = RoomLightsGroup::GetInstance();
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
    public static function Devices($type, $subtype = null){
        $instance = RoomLightsGroup::GetInstance();
        if(is_null($subtype)) return $instance->LoadAllWhere(['type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        return $instance->LoadAllWhere(['type'=>$subtype,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomLightsGroup();
}
?>