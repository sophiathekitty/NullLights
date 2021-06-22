<?php

class WeMoArchives extends clsModel {
    public $table_name = "WeMoArchives";
    public $fields = [
        [
            'Field'=>"guid",
            'Type'=>"varchar(34)",
            'Null'=>"NO",
            'Key'=>"PRI",
            'Default'=>"",
            'Extra'=>""
        ],[
            'Field'=>"mac_address",
            'Type'=>"varchar(100)",
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
    private static function GetInstance(){
        if(is_null(WeMoArchives::$sensors)) WeMoArchives::$sensors = new WeMoArchives();
        return WeMoArchives::$sensors;
    }
    /**
     * save a log
     * @param array $data the data array for the log being created
     * @return array the save report
     */
    public static function SaveLog(array $data){
        $sensors = WeMoArchives::GetInstance();
        $sensors->PruneField('created',WeeksToSeconds(Settings::LoadSettingsVar('wemo_archive_weeks',5)));
        if(!isset($data['created'])) $data['created'] = date("Y-m-d H:i:s");
        $data['guid'] = md5($data['mac_address'].$data['created']);
        return $sensors->Save($data);
    }
    /**
     * load recent archives for a wemo
     * @param string $mac_address the mac address of the wemo
     * @param int $days how many days back to go
     * @return array list of recent archives for the wemo 
     */
    public static function Recent($mac_address,$days){
        $sensors = WeMoArchives::GetInstance();
        $seconds = DaysToSeconds($days);
        return $sensors->LoadWhereFieldAfter(['mac_address'=>$mac_address],'created',date("Y-m-d H:i:s",time()-$seconds));
    }
    /**
     * gets the archive data for a month
     * @param string $mac_address the mac address of the wemo
     * @param int $month the month number (1-12)
     * @return array list of archive records for a month for the wemo
     */
    public static function Month($mac_address,$month){
        $sensors = WeMoArchives::GetInstance();
        return $sensors->LoadFieldBetweenWhere("`mac_address` = '$mac_address'",'created',date("Y-$month-1 00:00:00"),date("Y-$month-".date("t",strtotime(date("Y-$month-1 00:00:00")))." 23:59:59"));
    }
}
if(defined('VALIDATE_TABLES')){
    clsModel::$models[] = new WeMoArchives();
}
?>