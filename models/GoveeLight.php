<?php
/**
 * model for storing Govee light data
 */
class GoveeLights extends clsModel {
    public $table_name = "GoveeLights";
    public $fields = [
        [
            'Field'=>"mac_address",
            'Type'=>"varchar(32)",
            'Null'=>"NO",
            'Key'=>"PRI",
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
            'Field'=>"model",
            'Type'=>"varchar(100)",
            'Null'=>"YES",
            'Key'=>"",
            'Default'=>null,
            'Extra'=>""
        ],[
            'Field'=>"light_id",
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
     * @return GoveeLights|clsModel
     */
    private static function GetInstance(){
        if(is_null(GoveeLights::$instance)) GoveeLights::$instance = new GoveeLights();
        return GoveeLights::$instance;
    }
    /**
     * load all GoveeDevices
     * sorted by type and then subtype
     * @return array all of the Govee lights
     */
    public static function AllLights(){
        $instance = GoveeLights::GetInstance();
        return $instance->LoadAll();
    }
    /**
     * load a Govee by its mac address
     * @param string $mac_address the mac address of the Govee
     * @return array Govee data array
     */
    public static function MacAddress($mac_address){
        $instance = GoveeLights::GetInstance();
        return $instance->LoadWhere(['mac_address'=>$mac_address]);
    }
    /**
     * load all GoveeDevices for a light group
     * @param string $light_id the id of the light group
     * @return array Govee data array
     */
    public static function LightGroup($light_id){
        $instance = GoveeLights::GetInstance();
        return $instance->LoadAllWhere(['light_id'=>$light_id]);
    }
    /**
     * save the Govee
     * @param array $data the Govee data object
     * @param bool $remote_data is this being synced from another device (make sure we're not overriding fresher local data)
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveGovee(array $data, $remote_data = false){
        $instance = GoveeLights::GetInstance();
        $data = $instance->CleanData($data);
        Debug::Log("GoveeLight::SaveGovee",$data);
        if($data['state'] == "-1") unset($data['state']);
        $Govee = GoveeLights::MacAddress($data['mac_address']);
        if(is_null($Govee)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['mac_address'=>$data['mac_address']],$remote_data);
    }

    /**
     * load all the Govee lights in a room
     * @param int $room_id the room's id
     * @param string|null $subtype filter by subtype
     * @return array list of Govee data arrays
     */
    public static function UnGrouped(){
        //Debug::Log("GoveeLights::RoomLights",$room_id,$subtype);
        $instance = GoveeLights::GetInstance();
        return $instance->LoadAllWhere(['light_id'=>0]);
    }
    /**
     * load all of type (and subtype)
     * @param string $type filter by type or any if null
     * @param string|null $subtype filter by subtype or any if null
     * @return array list of Govee data arrays
     */
    public static function GoveeDevices(){
        $instance = GoveeLights::GetInstance();
        return $instance->LoadAll();
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new GoveeLights();
}
?>