<?php
class WeMoChart {
    /**
     * generates an empty hourly chart
     * @return array an empty hourly chart
     */
    public static function EmptyHourly(){
        $hourly = [];
        for($i = 0; $i < 24; $i++){
            $h = (string)$i;
            if($i < 10) $h = "0$i";
            $hourly[] = ['hour'=>$h];
        }
    }
    /**
     * generates the hourly charts for all the lights in a room
     * @param int $room_id the id of the room
     * @return array list of lights and their hourly charts
     */
    public static function HourlyWeMoRoomLog($room_id){
        $lights = WeMoLights::RoomLights($room_id);
        $logs = [];
        foreach($lights as $light){
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
        $where = "`mac_address` = '$mac_address'";
        $light = WemoLights::MacAddress($mac_address);
        $log = WeMoChart::WeMoDayData($where);
        $archive = WeMoArchiver::CalculateWeMoArchiveAverageHours(WeMoArchives::Recent($mac_address,Settings::LoadSettingsVar("weather_log_days",5)));
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
        for($i = 0; $i < 24; $i++){
            $log[$i]['average_a'] = ($log[$i]['average']+$archive["h$i"])/2;
            $log[$i]['archive'] = $archive["h$i"];
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
        $where = "`mac_address` = '$mac_address' AND `created` BETWEEN '$date 00:00:00' AND '$date 23:59:59'";
        $light = WeMoLights::MacAddress($mac_address);
        $log = WeMoChart::WeMoDayData($where);
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
        $day = [];
        for($h = 0; $h < 24; $h++){
            $day[$h] = WeMoChart::HourlyWeMoData($where,$h);
        }
        return $day;
    }
    /**
     * gets hour data for a date
     * @param string $where a where string for selecting the date
     * @param int $hour the hour
     * @return array the hour data array
     */
    private static function HourlyWeMoData($where,$hour){
        if($hour < 10) $hour = "0".$hour;
        $table = WeMoLogs::HourWhere($where,$hour);
        $data = ['hour'=>$hour,'on' => 0, 'off' => 0, 'error' => 0];
        foreach($table as $row){
            switch($row['state']){
                case 0:
                case "0":
                    $data['off']++;
                break;
                case 1:
                case "1":
                    $data['on']++;
                break;
                case 2:
                case "2":
                    $data['error']++;
                break;
            }
            if($row['error']) $data['error']++;
        }
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