<?php
class WeMoChart {
    /**
     * generates an empty hourly chart
     * @return array an empty hourly chart
     */
    public static function EmptyHourly(){
        Debug::Trace("WeMoChart::EmptyHourly");
        $hourly = [];
        for($i = 0; $i < 24; $i++){
            $h = (string)$i;
            if($i < 10) $h = "0$i";
            $hourly[] = ['hour'=>$h,'on'=>0,'off'=>0,'error'=>0,'count'=>0];
        }
    }
    /**
     * generates the hourly charts for all the lights in a room
     * @param int $room_id the id of the room
     * @return array list of lights and their hourly charts
     */
    public static function HourlyWeMoRoomLog($room_id){
        Debug::Trace("WeMoChart::HourlyWeMoRoomLog");
        $lights = WeMoLights::RoomWeMos($room_id);
        $logs = [];
        foreach($lights as $light){
            //$all = WeMoLogs::MacAddress($light['mac_address']);
            array_push($logs,["light"=>$light,"hourly"=>WeMoChart::HourlyWeMoLog($light['mac_address'])]);
        }
        return $logs;
    }
    /**
     * generates a detailed hourly chart for a wemo
     * @param string $mac_address the mac address of the wemo
     * @return array a detailed hourly chart
     */
    public static function HourlyWeMoLog($mac_address){
        Debug::Trace("WeMoChart::HourlyWeMoLog");
        $where = "`mac_address` = '$mac_address'";
        $light = WemoLights::MacAddress($mac_address);
        $log = WeMoChart::WeMoDayData($where);
        $archive = WeMoArchiver::CalculateWeMoArchiveAverageHours(WeMoArchives::Recent($mac_address,Settings::LoadSettingsVar("wemo_archive_chart_days",5)));
        $log = WeMoChart::WemoCombineArchiveWithLog($archive,$log);
        for($i = 0; $i < count($log); $i++){
            $alpha = round($log[$i]['average'] * 0.9,3);
            $log[$i]['color_c'] = "rgba(".$light['color'].",".$alpha.")";
            $alpha_a = round($log[$i]['average_a'] * 0.9,3);
            $log[$i]['color'] = "rgba(".$light['color'].",".$alpha_a.")";
            $alpha_a = round($log[$i]['archive'] * 0.9,3);
            $log[$i]['color_a'] = "rgba(".$light['color'].",".$alpha_a.")";
        }
        return $log;
    }
    /**
     * combines the hourly chart from wemo logs and an archive
     * @param array $archive the archive data
     * @param array $log the hourly chart from wemo logs
     * @return array the hourly chart with the archive added
     */
    public static function WemoCombineArchiveWithLog($archive,$log){    // archive data
        Debug::Trace("WeMoChart::WemoCombineArchiveWithLog");
        for($i = 0; $i < 24; $i++){
            $log[$i]['average_a'] = ($log[$i]['average']+$archive["h$i"])/2;
            $log[$i]['archive'] = $archive["h$i"];
            $h = (string)$i;
            if($i < 10) $h = "0$i";
            $log[$i]['hour'] = $h;
            if(is_nan($log[$i]['archive'])) $log[$i]['archive'] = $log[$i]['average'];
            if(is_nan($log[$i]['average_a'])) $log[$i]['average_a'] = $log[$i]['average'];
        }
        return $log;
    }
    /**
     * generates an hourly chart from the wemo logs on a specific date
     * @param string $mac_address the mac address of the wemo
     * @param string $date the date string
     * @return array the hourly chart for the specified date
     */
    public static function HourlyWeMoLogDate($mac_address,$date){
        Debug::Trace("WeMoChart::HourlyWeMoLogDate");
        $where = "`mac_address` = '$mac_address' AND `created` BETWEEN '$date 00:00:00' AND '$date 23:59:59'";
        $light = WeMoLights::MacAddress($mac_address);
        $log = WeMoChart::WeMoDayData($where);
        $logs = WeMoLogs::MacAddress($mac_address);
        
        for($i = 0; $i < count($log); $i++){
            $alpha = round($log[$i]['average'] * 0.9,3);
            $log[$i]['color'] = "rgba(".$light['color'].",".$alpha.")";
        }
        return $log;
    }
    /**
     * gets the hourly data for a day
     * @param string $where a where string for selecting the date
     * @return array returns hourly data for day
     */
    private static function WemoDayData($where){
        Debug::Trace("WeMoChart::WemoDayData");
        $logs = WeMoLogs::Where($where);
        $chart = WeMoChart::EmptyHourly();
        foreach($logs as $log){
            $h = (int)date("G",strtotime($log['created']));
            /*
            if(!isset($chart[(int)$h]['on'])) $chart[(int)$h]['on'] = 0;
            if(!isset($chart[(int)$h]['off'])) $chart[(int)$h]['off'] = 0;
            if(!isset($chart[(int)$h]['error'])) $chart[(int)$h]['error'] = 0;
            */
            $chart[$h]['count']++;
            $chart[$h]['on'] += $log['state'];
            $chart[$h]['error'] += $log['error'];
        }
        //$day = [];
        for($h = 0; $h < 24; $h++){
            //$day[$h] = WeMoChart::HourlyWeMoData($where,$h);
            if($chart[$h]['count']) $chart[$h]['average'] = $chart[$h]['on'] / $chart[$h]['count'];
            $chart[$h]['off'] = $chart[$h]['count'] - $chart[$h]['on'];
        }
        return $chart;
    }
    /**
     * gets hour data for a date
     * @param string $where a where string for selecting the date
     * @param int $hour the hour
     * @return array the hour data array
     */
    private static function HourlyWeMoData($where,$hour){
        Debug::Trace("WeMoChart::HourlyWeMoData");
        $table = WeMoLogs::HourWhere($where,$hour);
        if($hour < 10) $hour = "0".$hour;
        $data = ['hour'=>$hour,'on' => 0, 'off' => 0, 'error' => 0];
        foreach($table as $row){
            $data['on'] += $row['state'];
            if($row['error']) $data['error']++;
        }
        $data['off'] = count($table) - $data['on'];
        $data['average'] = 0;
        if($data['on'] > $data['off']){
            $data['state'] = 'on';
        } else {
            $data['state'] = 'off';
        }
        if(count($table) == 0){
            $data['state'] = 'unknown';
        } else {
            $data['average'] = round($data['on'] / count($table),3);
        }
        return $data;
    }
}
?>