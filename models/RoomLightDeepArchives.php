<?php
/**
 * this if for having an idea of when a light tends to be on each month by day of week.
 * the idea being that we tend to do different stuff on different days of the week and
 * during different seasons of the year
 * @todo needs static load functions
 */
class RoomLightDeepArchives extends clsModel {
    public $table_name = "RoomLightDeepArchives";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"day_of_week",
            'Type'=>"tinyint(4)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"month",
            'Type'=>"tinyint(4)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"id",
            'Type'=>"int(11)",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h0",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h1",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h2",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h3",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h4",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h5",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h6",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h7",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h8",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h9",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h10",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h11",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h12",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h13",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h14",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h15",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h16",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h17",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h18",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h19",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h20",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h21",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h22",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"h23",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"errors",
            'Type'=>"float",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];
    private static $sensors = null;
    /**
     * @return RoomLightDeepArchives|clsModel
     */
    private static function GetInstance():RoomLightDeepArchives{
        if(is_null(RoomLightDeepArchives::$sensors)) RoomLightDeepArchives::$sensors = new RoomLightDeepArchives();
        return RoomLightDeepArchives::$sensors;
    }
    /**
     * save a deep archive
     * @param array $data the data array for the room light archive to be saved
     */
    public static function SaveLog(array $data){ 
        $sensors = RoomLightDeepArchives::GetInstance();
        $data = $sensors->CleanData($data);
        $data['guid'] = md5($data['id'].$data['day_of_week'].$data['month']);
        return $sensors->Save($data);
    }
    /**
     * delete light
     * @param $light_id the id of the light to delete
     */
    public static function DeleteLight($light_id){
        $instance = RoomLightDeepArchives::GetInstance();
        return $instance->DeleteFieldValue('id',$light_id);
    }

}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomLightDeepArchives();
}

?>