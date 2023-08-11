<?php
/**
 * model for storing profile device light data
 */
class LightProfileDevice extends clsModel {
    public $table_name = "LightProfileDevice";
    public $fields = [
        [
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>"auto_increment"
        ],[
            'Field'=>"profile_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0",
            'Extra'=>""
        ],[
            'Field'=>"light_id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"state",
            'Type'=>"int(11)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"percent",
            'Type'=>"float",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"percent_min",
            'Type'=>"float",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"percent_max",
            'Type'=>"float",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"color",
            'Type'=>"varchar(30)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    /**
     * @return LightProfileDevice|clsModel
     */
    private static function GetInstance(){
        if(is_null(LightProfileDevice::$instance)) LightProfileDevice::$instance = new LightProfileDevice();
        return LightProfileDevice::$instance;
    }
    /**
     * load all lights
     * sorted by type and then subtype
     * @return array all of the profile device lights
     */
    public static function AllLights(){
        $instance = LightProfileDevice::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * load a profile devices for a profile id
     * @param string $profile_id the id of the profile
     * @return array profile device data array
     */
    public static function LightProfileId($profile_id){
        $instance = LightProfileDevice::GetInstance();
        return $instance->LoadAllWhere(['profile_id'=>$profile_id]);
    }
    /**
     * save the lighting profile
     * @param array $data the profile device data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLightProfile(array $data){
        $instance = LightProfileDevice::GetInstance();
        $light = LightProfileDevice::LightProfileId($data['id']);
        if($data['state'] == "nothing") return self::DeleteLightProfileDevice($data);
        if(is_null($light) || (int)$data['id'] == 0){
            $data = $instance->CleanDataSkipId($data);
            return $instance->Save($data);
        }
        $data = $instance->CleanData($data);
        return $instance->Save($data,['id'=>$data['id']]);
    }
    /**
     * delete a profile device
     * @param array $data the profile device data object
     * @return array delete report ['error'=>clsDB::$db_g->get_err(),'sql'=>$sql]
     */
    public static function DeleteLightProfileDevice(array $data){
        $instance = LightProfileDevice::GetInstance();
        return $instance->DeleteFieldValue('id',$data['id']);
    }
    /**
     * delete a profile device
     * @param array $data the profile device data object
     * @return array delete report ['error'=>clsDB::$db_g->get_err(),'sql'=>$sql]
     */
    public static function DeleteLightProfile(array $data){
        $instance = LightProfileDevice::GetInstance();
        return $instance->DeleteFieldValue('profile_id',$data['profile_id']);
    }
    /**
     * load all of the devices in a room
     * @param int $room_id the room's id
     * @return array list of profile device data arrays
     */
    public static function RoomDevices($room_id){
        $instance = LightProfileDevice::GetInstance();
        return $instance->LoadAllWhere(['room_id'=>$room_id]);
    }

}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new LightProfileDevice();
}
?>