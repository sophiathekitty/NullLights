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
            'Field'=>"snooze_minutes",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"max_on_hours",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
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
            'Field'=>"last_profile_on",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"last_on",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"last_off",
            'Type'=>"datetime",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
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
     * @param int $id the id of the light
     * @return array wemo data array
     */
    public static function LightId($id){
        $instance = RoomLightsGroup::GetInstance();
        return $instance->LoadWhere(['id'=>$id]);
    }
    /**
     * load a light by its id
     * @param string $name the id of the light
     * @return array light data array
     */
    public static function LightName($name){
        $instance = RoomLightsGroup::GetInstance();
        return $instance->LoadWhere(['name'=>$name]);
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
            $data['type'] = RoomLightsGroup::RoomLightType($data);
            $data['subtype'] = RoomLightsGroup::RoomLightSubType($data);    
        }
        $light = RoomLightsGroup::LightId($data['id']);
        if(is_null($light)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['id'=>$data['id']],$remote_data);
    }
    /**
     * delete light
     * @param $light_id the id of the light to delete
     */
    public static function DeleteLight($light_id){
        $instance = RoomLightsGroup::GetInstance();
        return $instance->DeleteFieldValue('id',$light_id);
    }
    /**
     * figure out what type of RoomLight this is based on keywords in the name
     * @param array $RoomLight the RoomLight data array
     * @return string light, fan, other
     */
    public static function RoomLightType($RoomLight){
        if(strpos(strtolower($RoomLight['name']),"blacklight") > -1) return "blacklight";
        if(strpos(strtolower($RoomLight['name']),"light") > -1) return "light";
        if(strpos(strtolower($RoomLight['name']),"lamp") > -1) return "light";
        if(strpos(strtolower($RoomLight['name']),"ambient") > -1) return "light";
        if(strpos(strtolower($RoomLight['name']),"fan") > -1) return "fan";
        return "other";
    }
    /**
     * figure out the subtype of RoomLight based on name
     * @param array $RoomLight the RoomLight data array
     * @return string lava, ambient, mood, lamp, inquisition, stars, window, other
     */
    public static function RoomLightSubType($RoomLight){
        if(strpos(strtolower($RoomLight['name']),"painting") > -1) return "painting";
        if(strpos(strtolower($RoomLight['name']),"lava") > -1) return "lava";
        if(strpos(strtolower($RoomLight['name']),"rope") > -1) return "rope";
        if(strpos(strtolower($RoomLight['name']),"strip") > -1) return "rope";
        if(strpos(strtolower($RoomLight['name']),"ambient") > -1) return "ambient";
        if(strpos(strtolower($RoomLight['name']),"mood") > -1) return "mood";
        if(strpos(strtolower($RoomLight['name']),"lamp") > -1) return "lamp";
        if(strpos(strtolower($RoomLight['name']),"inquisition") > -1) return "inquisition";
        if(strpos(strtolower($RoomLight['name']),"ceiling") > -1) return "ceiling";
        if(strpos(strtolower($RoomLight['name']),"stars") > -1) return "stars";
        if(strpos(strtolower($RoomLight['name']),"window") > -1) return "window";
        if(strpos(strtolower($RoomLight['name']),"desk") > -1) return "desk";
        return "other";
    }
    /**
     * add a device to a light group
     * @param $data the light to add to group
     * @return array a save report (or error report)
     */
    public static function GroupDevice(&$data){
        if(defined("DEVICE_GROUP_SERVICE")) Services::Log(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice");
        $instance = RoomLightsGroup::GetInstance();
        if(!isset($data['name'])) return ['error'=>"name field missing"];
        $name = $instance->RemoveTailNumber($data['name']);
        if(defined("DEVICE_GROUP_SERVICE")) Services::Log(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice::name - $name (".$data['name'].")");
        //Debug::Log("RoomLightsGroup::GroupDevice::name",["device"=>$data['name'],"group"=>$name]);
        //if(isset($data['light_id']) && (int)$data['light_id'] != 0) $light = RoomLightsGroup::LightId($data['light_id']);
        //else $light = RoomLightsGroup::LightName($name);
        $light = RoomLightsGroup::LightName($name);
        if(is_null($light) || $light['name'] != $name){
            // add a new light group
            if(defined("DEVICE_GROUP_SERVICE")) Services::Log(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice - add a new group");
            //Debug::Log("RoomLightsGroup::GroupDevice","add new group");
            $light = $instance->CleanDataSkipId($data);
            $light['name'] = $name;
            $save = RoomLightsGroup::SaveLight($light);
            //if($save['error'] != "" && defined("DEVICE_GROUP_SERVICE")) Services::Error(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice:Error - ".$save['error']);
            if(isset($save['last_insert_id']) && (int)$save['last_insert_id'] != 0){
                $data['light_id'] = $save['last_insert_id'];
                if(defined("DEVICE_GROUP_SERVICE")) Services::Log(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice - new light id: ".$data['light_id']);
                return $save;
            } else {
                if(defined("DEVICE_GROUP_SERVICE")) Services::Error(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice missing last_insert_id: ".$save['error']);
                if(defined("DEVICE_GROUP_SERVICE")) Services::Error(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice ".$save['sql']);
                return ['error'=>"missing last_insert_id. can't add device to group",'light'=>$light,'data'=>$data,'save'=>$save];
            }
        } else {
            // just update the device
            if(defined("DEVICE_GROUP_SERVICE")) Services::Log(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice - add device to existing group");
            //Debug::Log("RoomLightsGroup::GroupDevice","add device to existing group");
            $data['light_id'] = $light['id'];
            return ['group'=>$light];
        }
        if(defined("DEVICE_GROUP_SERVICE")) Services::Error(constant("DEVICE_GROUP_SERVICE"),"RoomLightsGroup::GroupDevice - not added to a group?");
        return ["error"=>"not added to a group?"];
    }    
    /**
     * add a wemo to a room light group (or create a new room light group if no match found)
     * @param array $data the wemo data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function GroupWemo($data){
        Debug::Log("RoomLightsGroup::GroupWemo::wemo",$data);
        $save = RoomLightsGroup::GroupDevice($data);
        $save['wemo_save'] = WeMoLights::SaveWeMo($data);
        Debug::Log("RoomLightsGroup::GroupWemo::save",$save);
        return $save;
        /*
        $instance = RoomLightsGroup::GetInstance();
        if(!isset($data['name'])) return ['error'=>"name field missing"];
        $name = $instance->RemoveTailNumber($data['name']);
        if(isset($data['light_id']) && (int)$data['light_id'] != 0) $light = RoomLightsGroup::LightId($data['light_id']);
        else $light = RoomLightsGroup::LightName($name);
        if(is_null($light) || $light['name'] != $name){
            // add a new light group
            $light = $instance->CleanData($data);
            $light['name'] = $name;
            $save = RoomLightsGroup::SaveLight($light);
            if(isset($save['last_insert_id'])){
                $data['light_id'] = $save['last_insert_id'];
                $save['wemo_save'] = WeMoLights::SaveWeMo($data);
                return $save;
            }
        } else {
            // just update the wemo
            $data['light_id'] = $light['id'];
            return WeMoLights::SaveWeMo($data);
        }
        return ["error"=>"not added to a group?"];
        */
    }
    /**
     * add a tuya to a room light group (or create a new room light group if no match found)
     * @param array $data the tuya data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function GroupTuya($data){
        Debug::Log("RoomLightsGroup::GroupTuya::tuya",$data);
        $save = RoomLightsGroup::GroupDevice($data);
        $save['tuya_save'] = TuyaLights::SaveTuya($data);
        Debug::Log("RoomLightsGroup::GroupTuya::save",$save);
        return $save;
        /*
        $instance = RoomLightsGroup::GetInstance();
        if(!isset($data['name'])) return ['error'=>"name field missing"];
        $name = $instance->RemoveTailNumber($data['name']);
        Services::Log("NullLights::Tuya::Discover","Group Name: ".$name);
        if(isset($data['light_id']) && (int)$data['light_id'] != 0) $light = RoomLightsGroup::LightId($data['light_id']);
        else $light = RoomLightsGroup::LightName($name);
        if((int)$data['room_id'] == 0) $data = RoomLightsGroup::FindRoomLightRoom($data);
        if(is_null($light)){
            // add a new light group
            Services::Log("NullLights::Tuya::Discover","add a new light group");
            $light = $instance->CleanDataSkipId($data);
            $light['name'] = $name;
            $save = RoomLightsGroup::SaveLight($light);
            Services::Log("NullLights::Tuya::Discover","Error".$save['error']);
            if(isset($save['last_insert_id'])){
                $data['light_id'] = $save['last_insert_id'];
                Services::Log("NullLights::Tuya::Discover","light_id: ".$data['light_id']);
                $save['tuya_save'] = TuyaLights::SaveTuya($data);
                Services::Log("NullLights::Tuya::Discover","Error".$save['tuya_save']['error']);
                return $save;
            } else {
                Services::Log("NullLights::Tuya::Discover","last_insert_id not set?");
            }
        } else {
            // just update the tuya
            Services::Log("NullLights::Tuya::Discover","just update the tuya");
            $data['light_id'] = $light['id'];
            $light['name'] = $name;
            if($light['room_id'] == 0) $light = RoomLightsGroup::FindRoomLightRoom($light);
            $save = RoomLightsGroup::SaveLight($light);
            return TuyaLights::SaveTuya($data);
        }
        return ["error"=>"not added to a group?"];
        */
    }
    /**
     * add a govee to a room light group (or create a new room light group if no match found)
     * @param array $data the govee data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function GroupGovee($data){
        Debug::Log("RoomLightsGroup::GroupGovee::govee",$data);
        $save = RoomLightsGroup::GroupDevice($data);
        $save['govee_save'] = GoveeLights::SaveGovee($data);
        Debug::Log("RoomLightsGroup::GroupGovee::save",$save);
        return $save;
        /*
        $instance = RoomLightsGroup::GetInstance();
        if(!isset($data['name'])) return ['error'=>"name field missing"];
        $name = $instance->RemoveTailNumber($data['name']);
        if(isset($data['light_id']) && (int)$data['light_id'] != 0) $light = RoomLightsGroup::LightId($data['light_id']);
        else $light = RoomLightsGroup::LightName($name);
        if(is_null($light)){
            // add a new light group
            $light = $instance->CleanData($data);
            $light['name'] = $name;
            $save = RoomLightsGroup::SaveLight($light);
            if(isset($save['last_insert_id'])){
                $data['light_id'] = $save['last_insert_id'];
                $save['govee_save'] = GoveeLights::SaveGovee($data);
                return $save;
            }
        } else {
            // just update the govee
            $data['light_id'] = $light['id'];
            return GoveeLights::SaveGovee($data);
        }
        return ["error"=>"not added to a group?"];
        */
    }
    /**
     * load all the lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of data arrays
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
        if(is_null($type) && is_null($subtype)) {
            //Services::Log("NullLights::AutomationLegacy","RoomLightsGroup::RoomDevices (1) room_id: $room_id - type: $type - subtype: $subtype");
            return $instance->LoadAllWhere(['room_id'=>$room_id],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        }
        if(is_null($subtype)) {
            //Services::Log("NullLights::AutomationLegacy","RoomLightsGroup::RoomDevices (2) room_id: $room_id - type: $type - subtype: $subtype");
            return $instance->LoadAllWhere(['room_id'=>$room_id,'type'=>$type],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        }
        //Services::Log("NullLights::AutomationLegacy","RoomLightsGroup::RoomDevices (3) room_id: $room_id - type: $type - subtype: $subtype");
        $devices = $instance->LoadAllWhere(['room_id'=>$room_id,'type'=>$type,'subtype'=>$subtype],["room_id"=>"ASC","type"=>"ASC","subtype"=>"ASC"]);
        //Services::Log("NullLights::AutomationLegacy","RoomLightsGroup::RoomDevices sql: ".clsDB::$db_g->last_sql);
        return $devices;
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
    /**
     * find the room the light is in
     * @param array $data the data array
     * @return array the $data array with the room id set
     */
    public static function FindRoomLightRoom($data){
        $rooms = Rooms::AllRooms();
        foreach($rooms as $room){
            if (stripos(strtolower($data['name']), strtolower($room['name'])) !== false) {
                // The $data['name'] contains $room['name']
                $data['room_id'] = $room['id'];
                return $data;
            }            
        }
        return $data;
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomLightsGroup();
}
?>