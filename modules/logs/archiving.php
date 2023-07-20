<?php
/**
 * module for creating archives from logs and deep archives from archives
 * @depreciated use RoomLightGroupArchiver to work with the RoomLightsGroup instead
 */
class WeMoArchiver {
    /**
     * archive yesterday?
     */
    public static function ArchiveYesterday($wemo){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveYesterday");
        return WeMoArchiver::ArchiveWeMoLogsYesterday($wemo['mac_address']);
    }
    /**
     * converts an hourly chart to an archive
     * @param array $hourly the hourly chart 
     * @return array returns the data object for the archive
     */
    public static function HourlyToArchive(array $hourly){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"HourlyToArchive");
        $archive = [];
        for($i = 0; $i < 24; $i++){
            $archive["h$i"] = $hourly[$i]['average'];
        }
        return $archive;
    }
    /**
     * converts an archive data array to an hourly chart array
     * @param array $archive archive data array
     * @param array|null $hourly the hourly chart that's having the archive added to it
     * @return array the hourly chart array
     */
    public static function ArchiveToHourly(array $archive, $hourly = null){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveToHourly");
        if(is_null($hourly)) $hourly = WeMoChart::EmptyHourly();
        for($i = 0; $i < 24; $i++){
            $hourly[$i]['average_a'] = $archive["h$i"];
        }
        return $hourly;
    }
    /**
     * archive today's wemo logs
     * @param string $mac_address wemo mac address to archive
     * @return array returns the save report
     */
    public static function ArchiveWeMoLogsToday($mac_address){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveWeMoLogsToday $mac_address");
        return WeMoArchiver::ArchiveWeMoLogsDate($mac_address,date("Y-m-d"));
    }
    /**
     * archive yesterday's wemo logs
     * @param string $mac_address wemo mac address to archive
     * @return array returns the save report
     */
    public static function ArchiveWeMoLogsYesterday($mac_address){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveWeMoLogsYesterday $mac_address ".date("Y-m-d",time()-DaysToSeconds(1)));
        return WeMoArchiver::ArchiveWeMoLogsDate($mac_address,date("Y-m-d",time()-DaysToSeconds(1)));
    }
    /**
     * archive wemo logs from a specific date
     * @param string $mac_address wemo mac address to archive
     * @param string $date the date to be archived
     * @return array returns the save report
     */
    public static function ArchiveWeMoLogsDate($mac_address,$date){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveWeMoLogsDate $mac_address,$date");
        $log = WeMoChart::HourlyWeMoLogDate($mac_address,$date);
        $errors = 0;
        foreach($log as $l){
            $errors += $l['error'];
        }
        //print_r($log);
        $archive = WeMoArchiver::HourlyToArchive($log);
        $archive['mac_address'] = $mac_address;
        $archive['errors'] = $errors;
        $res = WeMoArchives::SaveLog($archive);
        Debug::Log("WeMoArchives::SaveLog",$res);
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"ArchiveWeMoLogsDate err? ".$res['error']);
        return $res;
    }
    /**
     * make a deep archive from a month's archive
     * @param string $mac_address wemo mac address to deep archive
     * @param string $month the month to be deep archived
     */
    public static function DeepArchiveWeMoArchiveMonth($mac_address,$month){
        if(defined("ARCHIVE_SERVICE"))Services::Log("WeMoArchiver::DeepArchive","DeepArchiveWeMoArchiveMonth $mac_address,$month");
        $archive = WeMoArchives::Month($mac_address,$month);
        $week = [];
        foreach($archive as $day){
            $day_of_week = date("N",strtotime($day['created']));
            if(defined("ARCHIVE_SERVICE"))Services::Log("WeMoArchiver::DeepArchive","DeepArchiveWeMoArchiveMonth--day_of_week $day_of_week");
            if(isset($week[$day_of_week])){
                $week[$day_of_week]['count']++;
                $week[$day_of_week]['day'] = WeMoArchiver::WeMoArchiveAddHours($week[$day_of_week]['day'],$day);
            } else {
                $week[$day_of_week] = [];
                $week[$day_of_week]['count'] = 1;
                $week[$day_of_week]['day'] = $day;
            }
        }
        for($i = 1; $i <= 7; $i++){
            $a = WeMoArchiver::WeMoArchiveAverageHours($week[$i]['day'],$week[$i]['count']);
            $a['mac_address'] = $mac_address;
            $a['day_of_week'] = $i;
            $a['month'] = $month;
            WeMoDeepArchives::SaveLog($a);
        }
    }
    /**
     * averages a list of archive days into one set
     * @param array $archives the list of archives
     * @return array the average archive of the archives
     */
    public static function CalculateWeMoArchiveAverageHours($archives){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"CalculateWeMoArchiveAverageHours");
        $archive = $archives[0];
        for($i = 1; $i < count($archives); $i++){
            $archive = WeMoArchiver::WeMoArchiveAddHours($archive,$archives[$i]);
        }
        return WeMoArchiver::WeMoArchiveAverageHours($archive,count($archives));
    }
    /**
     * adds two archives together
     * @param array $a the first archive
     * @param array $b the second archive
     * @return array the archives added together
     */
    private static function WeMoArchiveAddHours($a,$b){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"WeMoArchiveAddHours");
        for($h = 0; $h < 24; $h++){
            $a["h$h"] += $b["h$h"];
        }    
        $a['errors'] += $b['errors'];
        return $a;
    }
    /**
     * does the deciding part of averaging the archives
     * @param array $a the added up archive
     * @param int $count the number of archives in the
     * @return array the averaged archive
     */
    private static function WeMoArchiveAverageHours($a, $count){
        if(defined("ARCHIVE_SERVICE"))Services::Log(constant("ARCHIVE_SERVICE"),"WeMoArchiveAverageHours");
        for($h = 0; $h < 24; $h++){
            $a["h$h"] = $a["h$h"]/$count;
        }    
        $a['errors'] = $a['errors']/$count;
        return $a;
    }
}

/**
 * chat gpt code
 */

/**
 * Module for creating archives from logs and deep archives from archives
 */
class RoomLightGroupArchiver {
    /**
     * Archive yesterday?
     */
    public static function ArchiveYesterday($roomLightGroup) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiver::ArchiveYesterday");
        }
        return RoomLightGroupArchiver::ArchiveRoomLightGroupLogsYesterday($roomLightGroup['light_id']);
    }
    
    /**
     * Converts an hourly chart to an archive
     * @param array $hourly The hourly chart
     * @return array Returns the data object for the archive
     */
    public static function HourlyToArchive(array $hourly) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiver::HourlyToArchive");
        }
        $archive = [];
        for ($i = 0; $i < 24; $i++) {
            $archive["h$i"] = $hourly[$i]['average'];
        }
        return $archive;
    }
    
    /**
     * Converts an archive data array to an hourly chart array
     * @param array $archive Archive data array
     * @param array|null $hourly The hourly chart that's having the archive added to it
     * @return array The hourly chart array
     */
    public static function ArchiveToHourly(array $archive, $hourly = null) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiver::ArchiveToHourly");
        }
        if (is_null($hourly)) {
            $hourly = RoomLightChart::EmptyHourly();
        }
        for ($i = 0; $i < 24; $i++) {
            $hourly[$i]['average_a'] = $archive["h$i"];
        }
        return $hourly;
    }
    
    /**
     * Archive today's room light group logs
     * @param string $light_id Light ID to archive
     * @return array Returns the save report
     */
    public static function ArchiveRoomLightGroupLogsToday($light_id) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "ArchiveRoomLightGroupLogsToday $light_id");
        }
        return RoomLightGroupArchiver::ArchiveRoomLightGroupLogsDate($light_id, date("Y-m-d"));
    }
    
    /**
     * Archive yesterday's room light group logs
     * @param string $light_id Light ID to archive
     * @return array Returns the save report
     */
    public static function ArchiveRoomLightGroupLogsYesterday($light_id) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiver::ArchiveRoomLightGroupLogsYesterday $light_id " . date("Y-m-d", time() - DaysToSeconds(1)));
        }
        return RoomLightGroupArchiver::ArchiveRoomLightGroupLogsDate($light_id, date("Y-m-d", time() - DaysToSeconds(1)));
    }
    
    /**
     * Archive room light group logs from a specific date
     * @param string $light_id Light ID to archive
     * @param string $date The date to be archived
     * @return array Returns the save report
     */
    public static function ArchiveRoomLightGroupLogsDate($light_id, $date) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "ArchiveRoomLightGroupLogsDate $light_id, $date");
        }
        $log = RoomLightChart::HourlyRoomLightLogDate($light_id, $date);
        $errors = 0;
        foreach ($log as $l) {
            $errors += $l['error'];
        }
        $archive = RoomLightGroupArchiver::HourlyToArchive($log);
        $archive['id'] = $light_id;
        $archive['errors'] = $errors;
        $res = RoomLightArchives::SaveLog($archive);
        Debug::Log("RoomLightGroupArchives::SaveLog", $res);
        if (defined("ARCHIVE_SERVICE")) {
            if($res['error'] != "") Services::Error(constant("ARCHIVE_SERVICE"), "ArchiveRoomLightGroupLogsDate err? " . $res['error']);
        }
        return $res;
    }
    
    /**
     * Make a deep archive from a month's archive
     * @param string $light_id Light ID to deep archive
     * @param string $month The month to be deep archived
     */
    public static function DeepArchiveRoomLightGroupArchiveMonth($light_id, $month) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log("RoomLightGroupArchiver::DeepArchive", "DeepArchiveRoomLightGroupArchiveMonth $light_id, $month");
        }
        $archive = RoomLightArchives::Month($light_id, $month);
        $week = [];
        foreach ($archive as $day) {
            $day_of_week = date("N", strtotime($day['created']));
            if (defined("ARCHIVE_SERVICE")) {
                Services::Log("RoomLightGroupArchiver::DeepArchive", "DeepArchiveRoomLightGroupArchiveMonth--day_of_week $day_of_week");
            }
            if (isset($week[$day_of_week])) {
                $week[$day_of_week]['count']++;
                $week[$day_of_week]['day'] = RoomLightGroupArchiver::RoomLightGroupArchiveAddHours($week[$day_of_week]['day'], $day);
            } else {
                $week[$day_of_week] = [];
                $week[$day_of_week]['count'] = 1;
                $week[$day_of_week]['day'] = $day;
            }
        }
        for ($i = 1; $i <= 7; $i++) {
            $a = RoomLightGroupArchiver::RoomLightGroupArchiveAverageHours($week[$i]['day'], $week[$i]['count']);
            $a['id'] = $light_id;
            $a['day_of_week'] = $i;
            $a['month'] = $month;
            RoomLightDeepArchives::SaveLog($a);
        }
    }
    
    /**
     * Averages a list of archive days into one set
     * @param array $archives The list of archives
     * @return array The average archive of the archives
     */
    public static function CalculateRoomLightGroupArchiveAverageHours($archives) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "CalculateRoomLightGroupArchiveAverageHours");
        }
        $archive = $archives[0];
        for ($i = 1; $i < count($archives); $i++) {
            $archive = RoomLightGroupArchiver::RoomLightGroupArchiveAddHours($archive, $archives[$i]);
        }
        return RoomLightGroupArchiver::RoomLightGroupArchiveAverageHours($archive, count($archives));
    }
    
    /**
     * Adds two archives together
     * @param array $a The first archive
     * @param array $b The second archive
     * @return array The archives added together
     */
    private static function RoomLightGroupArchiveAddHours($a, $b) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiveAddHours");
        }
        for ($h = 0; $h < 24; $h++) {
            $a["h$h"] += $b["h$h"];
        }
        $a['errors'] += $b['errors'];
        return $a;
    }
    
    /**
     * Does the deciding part of averaging the archives
     * @param array $a The added up archive
     * @param int $count The number of archives in the
     * @return array The averaged archive
     */
    private static function RoomLightGroupArchiveAverageHours($a, $count) {
        if (defined("ARCHIVE_SERVICE")) {
            Services::Log(constant("ARCHIVE_SERVICE"), "RoomLightGroupArchiveAverageHours");
        }
        for ($h = 0; $h < 24; $h++) {
            $a["h$h"] = $a["h$h"] / $count;
        }
        $a['errors'] = $a['errors'] / $count;
        return $a;
    }
}

?>