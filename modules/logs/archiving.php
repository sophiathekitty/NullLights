<?php
/**
 * module for creating archives from logs and deep archives from archives
 */
class WeMoArchiver {
    public static function ArchiveYesterday($wemo){
        $chart = WeMoChart::HourlyWeMoLog($wemo['mac_address']);
    }
    /**
     * converts an hourly chart to an archive
     * @param array $hourly the hourly chart 
     * @return array returns the data object for the archive
     */
    public static function HourlyToArchive(array $hourly){
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
        return WeMoArchiver::ArchiveWeMoLogsDate($mac_address,date("Y-m-d"));
    }
    /**
     * archive yesterday's wemo logs
     * @param string $mac_address wemo mac address to archive
     * @return array returns the save report
     */
    public static function ArchiveWeMoLogsYesterday($mac_address){
        return WeMoArchiver::ArchiveWeMoLogsDate($mac_address,date("Y-m-d",time()-DaysToSeconds(1)));
    }
    /**
     * archive wemo logs from a specific date
     * @param string $mac_address wemo mac address to archive
     * @param string $date the date to be archived
     * @return array returns the save report
     */
    public static function ArchiveWeMoLogsDate($mac_address,$date){
        $log = WeMoChart::HourlyWeMoLogDate($mac_address,$date);
        $errors = 0;
        foreach($log as $l){
            $errors += $l['error'];
        }
        //print_r($log);
        $archive = WeMoArchiver::HourlyToArchive($log);
        $archive['mac_address'] = $mac_address;
        return WeMoArchives::SaveLog($archive);
    }
    /**
     * make a deep archive from a month's archive
     * @param string $mac_address wemo mac address to deep archive
     * @param string $month the month to be deep archived
     */
    public static function DeepArchiveWeMoArchiveMonth($mac_address,$month){
        $archive = WeMoArchives::Month($mac_address,$month);
        $week = [];
        foreach($archive as $day){
            $day_of_week = date("N",strtotime($day['created']));
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
        for($h = 0; $h < 24; $h++){
            $a["h$h"] = $a["h$h"]/$count;
        }    
        $a['errors'] = $a['errors']/$count;
        return $a;
    }
}
?>