<?php
/**
 * model for storing profile device light data
 */
class ActiveLightProfiles extends clsModel {
    public $table_name = "ActiveLightProfiles";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(100)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"profile_id",
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
            'Field'=>"created",
            'Type'=>"datetime",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"current_timestamp()",
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    /**
     * @return ActiveLightProfiles|clsModel
     */
    private static function GetInstance(){
        if(is_null(ActiveLightProfiles::$instance)) ActiveLightProfiles::$instance = new ActiveLightProfiles();
        return ActiveLightProfiles::$instance;
    }
    /**
     * load all ActiveLightProfiles
     * sorted by type and then subtype
     * @return array all of the profile device ActiveLightProfiles
     */
    public static function AllActiveLightProfiles(){
        $instance = ActiveLightProfiles::GetInstance();
        return $instance->LoadAll();
    }
    /** 
     * load all active profile devices joined to the active light profiles on profile_id
     * @return array a joined data array of all the profile devices 
     */
    public static function AllActiveLightProfileDevices(){
        $instance = ActiveLightProfiles::GetInstance();
        return $instance->JoinFieldsWhere(new LightProfileDevice(),["created"],["light_id","state","color","percentage"],"profile_id","profile_id",null,null);
    }
    /**
     * save the lighting profile
     * @param array $data the profile device data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLightProfile(array $data){
        $instance = ActiveLightProfiles::GetInstance();
        $data = $instance->CleanData($data);
        $data = $instance->AddGUID($data,"profile_id","created");
        return $instance->Save($data);
    }
    /**
     * load active profile for room
     * @param int $room_id the room's id
     * @return array list of profile device data arrays
     */
    public static function RoomActiveProfile($room_id){
        $instance = ActiveLightProfiles::GetInstance();
        return $instance->LoadWhere(['room_id'=>$room_id]);
    }
    /**
     * apply lighting profile to room. deletes any other active profiles with the same room id
     * @param array $data the LightingProfile data array
     */
    public static function ApplyLightingProfile($data){
        $instance = ActiveLightProfiles::GetInstance();
        $data['profile_id'] = $data['id'];
        $data = $instance->CleanData($data);
        $data = $instance->AddGUID($data,"profile_id","created");
        $instance->DeleteFieldValue('room_id',$data['room_id']);
        return $instance->Save($data);
    }
    /**
     * clear a room's lighting profile
     * @param int $room_id the room id to be cleared
     */
    public static function ClearRoomLightingProfile($room_id){
        $instance = ActiveLightProfiles::GetInstance();
        $instance->DeleteFieldValue('room_id',$room_id);
    }
    /**
     * clear a lighting profile
     * @param array $data the LightingProfile data array
     */
    public static function ClearLightingProfile($data){
        $instance = ActiveLightProfiles::GetInstance();
        if(isset($data['room_id'])) $instance->DeleteFieldValue('room_id',$data['room_id']);
        else Debug::Log("LightingProfile::ClearLightingProfile", "[Error]Missing room_id",$data);
    }
    /**
     * prune profiles that are over a day old 
     */
    public static function PruneOld(){
        $instance = ActiveLightProfiles::GetInstance();
        $instance->PruneField("created",DaysToSeconds(1));
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new ActiveLightProfiles();
}
?>