<?php
/**
 * first level of light archives. archiving the hourly chart for the room light groups so we can
 * track when they are on during the day should try to keep a months worth of these for
 * doing the final level of archiving
 */
class RoomLightArchives extends clsModel {
    public $table_name = "RoomLightArchives";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
            'Null'=>"NO",
            'Key'=>"PRI",
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
        ],[
            'Field'=>"created",
            'Type'=>"date",
            'Null'=>"NO",
            'Key'=>"",
            'Default'=>"",
            'Extra'=>""
        ]
    ];
    private static $sensors = null;
    private static function GetInstance():RoomLightArchives{
        if(is_null(RoomLightArchives::$sensors)) RoomLightArchives::$sensors = new RoomLightArchives();
        return RoomLightArchives::$sensors;
    }
    /**
     * save a log
     * @param array $data the data array for the log being created
     * @return array the save report
     */
    public static function SaveLog(array $data){
        $sensors = RoomLightArchives::GetInstance();
        $sensors->PruneField('created',WeeksToSeconds(Settings::LoadSettingsVar('room_light_archive_weeks',5)));
        $data = $sensors->CleanData($data);
        if(!isset($data['created'])) $data['created'] = date("Y-m-d H:i:s");
        $data['guid'] = md5($data['id'].$data['created']);
        return $sensors->Save($data);
    }
    /**
     * delete light
     * @param $light_id the id of the light to delete
     */
    public static function DeleteLight($light_id){
        $instance = RoomLightArchives::GetInstance();
        return $instance->DeleteFieldValue('id',$light_id);
    }
    /**
     * load recent archives for a room light group
     * @param string $id the mac address of the room light group
     * @param int $days how many days back to go
     * @return array list of recent archives for the room light group 
     */
    public static function Recent($id,$days){
        $sensors = RoomLightArchives::GetInstance();
        $seconds = DaysToSeconds($days);
        return $sensors->LoadWhereFieldAfter(['id'=>$id],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    /**
     * gets the archive data for a month
     * @param string $id the mac address of the room light group
     * @param int $month the month number (1-12)
     * @return array list of archive records for a month for the room light group
     */
    public static function Month($id,$month){
        $sensors = RoomLightArchives::GetInstance();
        return $sensors->LoadFieldBetweenWhere("`id` = '$id'",'created',date("Y-$month-1 00:00:00"),date("Y-$month-".date("t",strtotime(date("Y-$month-1 00:00:00")))." 23:59:59"));
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new RoomLightArchives();
}
?>