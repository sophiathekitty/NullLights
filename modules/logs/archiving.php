<?php
class WeMoArchiver {

    

    public static function HourlyToArchive(array $hourly){
        $archive = [];
        for($i = 0; $i < 24; $i++){
            $archive["h$i"] = $hourly[$i]['average'];
        }
        return $archive;
    }
    public static function ArchiveToHourly(array $archive, $hourly = null){
        if(is_null($hourly)) $hourly = WeMoArchiver::EmptyHourly();
        for($i = 0; $i < 24; $i++){
            $hourly[$i]['average_a'] = $archive["h$i"];
        }
        return $hourly;
    }
    private static function EmptyHourly(){
        $hourly = [];
        for($i = 0; $i < 24; $i++){
            $h = (string)$i;
            if($i < 10) $h = "0$i";
            $hourly[] = ['hour'=>$h];
        }
    }
}
?>