<?php
/**
 * model for storing lighting profiles light data
 */
class LightingProfile extends clsModel {
    public $table_name = "LightingProfile";
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
            'Field'=>"type",
            'Type'=>"varchar(10)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"light",
            'Extra'=>""
        ],[
            'Field'=>"light_level_min",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"0.5",
            'Extra'=>""
        ],[
            'Field'=>"light_level_max",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"1.5",
            'Extra'=>""
        ]
    ];
    private static $instance = null;
    /**
     * @return LightingProfile|clsModel
     */
    private static function GetInstance(){
        if(is_null(LightingProfile::$instance)) LightingProfile::$instance = new LightingProfile();
        return LightingProfile::$instance;
    }
    /**
     * load all lighting profiles
     * sorted by room_id and then type
     * @return array all of the lighting profiles
     */
    public static function AllProfiles(){
        $instance = LightingProfile::GetInstance();
        return $instance->LoadAllWhere(null,["room_id"=>"ASC","type"=>"ASC"]);
    }
    /**
     * load a lighting profiles by its id
     * @param string $id the id of the light
     * @return array lighting profiles data array
     */
    public static function LightProfileId($id){
        $instance = LightingProfile::GetInstance();
        return $instance->LoadWhere(['id'=>$id]);
    }
    /**
     * save the lighting profile
     * @param array $data the lighting profiles data object
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SaveLightProfile(array $data){
        $instance = LightingProfile::GetInstance();
        $data = $instance->CleanData($data);
        $light = LightingProfile::LightProfileId($data['id']);
        if(is_null($light)){
            return $instance->Save($data);
        }
        return $instance->Save($data,['id'=>$data['id']]);
    }
    /**
     * delete a lighting profile
     */
    public static function DeleteLightProfile($id){
        $instance = LightingProfile::GetInstance();
        return $instance->DeleteFieldValue('id',$id);
    }
    /**
     * load all of the lighting profiles in a room
     * @param int $room_id the room's id
     * @return array list of lighting profiles data arrays
     */
    public static function RoomId($room_id){
        $instance = LightingProfile::GetInstance();
        return $instance->LoadAllWhere(['room_id'=>$room_id],['light_level_min'=>"ASC",'light_level_max'=>"ASC"]);
    }

}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new LightingProfile();
}
?>