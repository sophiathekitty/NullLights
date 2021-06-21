<?php
/**
 * A collection of static function for calculating how long lights have been on or off
 */
class WemoTime {
    private static function OnTimeCalculate($wemo){
        $last_on = WeMoLogs::LastOn($wemo['mac_address']);
        $last_off = WeMoLogs::LastOff($wemo['mac_address']);
        $on_time = strtotime($last_on['created']);
        $off_time = strtotime($last_off['created']);
        $time = $on_time - $off_time;
        //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
        return $time;            
    }
    /**
     * how long a light has been on consecutively
     * @param {Array} $wemo WeMoLight data array
     * @return {int} time in seconds
     */
    public static function OnTime($wemo){
        $time = WemoTime::OnTimeCalculate($wemo);
        if($time < 0) $time = 0;
        return $time;            
    }
    /**
     * how long a light has been off consecutively
     * @param {Array} $wemo WeMoLight data array
     * @return {int} time in seconds
     */
    public static function OffTime($wemo){
        $time = WemoTime::OnTimeCalculate($wemo) * -1;
        if($time < 0) $time = 0;
        return $time;                    
    }
    /**
     * how long a light has been on consecutively in minutes
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in minutes
     */
    public static function OnMinutes($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToMinutes($time);
    }
    /**
     * how long a light has been off consecutively in minutes
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in minutes
     */
    public static function OffMinutes($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToMinutes($time);
    }
    /**
     * how long a light has been on consecutively in hours
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in hours
     */
    public static function OnHours($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToHours($time);
    }
    /**
     * how long a light has been off consecutively in hours
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in hours
     */
    public static function OffHours($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToHours($time);
    }
    /**
     * how long a light has been on consecutively in days
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in days
     */
    public static function OnDays($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToDays($time);
    }
    /**
     * how long a light has been off consecutively in days
     * @param {Array} $wemo WeMoLight data array
     * @return {float} time in days
     */
    public static function OffDays($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToDays($time);
    }
    /**
     * the cumulative time a light has been on during a time window
     * @param {Array} $wemo WeMoLight data array
     * @param {Array} $time time window to check in seconds
     * @return {int|float} time in seconds
     */
    public static function OnDuringTime($wemo,$time){
        $log = WeMoLogs::Recent($wemo['mac_address'],$time);
        if(count($log) == 0)
            return 0;
        $start_time = strtotime($log[0]['created']);
        $start_on = $log[0]['state'];
        $on_time = 0;
        for($i = 1; $i < count($log); $i++){
            $end_time = strtotime($log[$i]['created']);
            $end_on = $log[$i]['state'];
            $time_diff = $end_time - $start_time;
            if($start_on == 1 || $start_on == "1"){
                $on_time += $time_diff;
            }
            $start_time = $end_time;
            $start_on = $end_on;
        }
        return $on_time;    
    }
    /**
     * how long the lights have been on in room
     * @param {Array} $wemo WeMoLight data array
     * 
     */
    public static function RoomOnTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id']);
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lights have been off in room
     * @param {Array} $wemo WeMoLight data array
     */
    public static function RoomOffTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id']);
        $time = null;
        foreach($lights as $light){
            $t = WemoTime::OffTime($light);
            if(is_null($time)) $time = $t;
            if($t < $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lights have been on in room
     * @param {Array} $wemo WeMoLight data array
     * 
     */
    public static function NeighborsOnTime($wemo){
        $neighbors = RoomNeighbors::Neighbors($wemo['room_id']);
        $time = 0;
        foreach($neighbors as $room_id){
            $t = WemoTime::RoomOnTime($room_id);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lights have been off in room
     * @param {Array} $wemo WeMoLight data array
     */
    public static function NeighborsOffTime($wemo){
        $neighbors = RoomNeighbors::Neighbors($wemo['room_id']);
        $time = null;
        foreach($neighbors as $room_id){
            $t = WemoTime::RoomOffTime($room_id);
            if(is_null($time)) $time = $t;
            if($t < $time) $time = $t;
        }
        return $time;
    }
    /**
     * check if a room has a lamp on
     * @param {Array} $wemo WeMoLight data array
     * @return bool returns true if room has lamp light on
     */
    public static function RoomLampIsOn($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"lamp");
        foreach($lights as $light){
            if((int)$light['state'] == 1){
                return true;
            }
        }
        return false;
    }
    /**
     * check if a room has a mood light on
     * @param {Array} $wemo WeMoLight data array
     * @return bool returns true if room has mood light on
     */
    public static function RoomMoodIsOn($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"mood");
        foreach($lights as $light){
            if((int)$light['state'] == 1){
                return true;
            }
        }
        return false;
    }
    /**
     * how long the lamp has been on in a room
     * @param {Array} $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomLampOnTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"lamp");
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param {Array} $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomLampOffTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"lamp");
        $time = null;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if(is_null($time)) $time = $t;
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param {Array} $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomMoodOnTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"mood");
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param {Array} $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomMoodOffTime($wemo){
        $lights = WeMoLights::RoomLights($wemo['room_id'],"mood");
        $time = null;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if(is_null($time)) $time = $t;
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * what percent of the logs recorded an error with the past $days
     * @param array $wemo the wemo json array. needs mac_address
     * @return float percent as 0 to 1
     */
    public static function WeMoErrorPercent($wemo,$days = 14){
        $logs = WeMoLogs::Recent($wemo['mac_address'],DaysToSeconds($days));
        $errors = 0;
        foreach($logs as $log){
            if((int)$log['state'] == 2){
                $errors++;
            }
        }
        return ($errors / count($logs));
    }
}
















//
// how long an individual light has been on within a recent time window
//
function WeMoOnTime($mac_address,$seconds){
    $log = WeMoLogs::Recent($mac_address,$seconds);
    if(count($log) == 0)
        return 0;
    $start_time = strtotime($log[0]['created']);
    $start_on = $log[0]['state'];
    $on_time = 0;
    for($i = 1; $i < count($log); $i++){
        $end_time = strtotime($log[$i]['created']);
        $end_on = $log[$i]['state'];
        $time_diff = $end_time - $start_time;
        if($start_on == 1 || $start_on == "1"){
            $on_time += $time_diff;
        }
        $start_time = $end_time;
        $start_on = $end_on;
    }
    return $on_time;
}
function WeMoOnNowTimeCalculate($mac_address){
    $last_on = WeMoLogs::LastOn($mac_address);
    $last_off = WeMoLogs::LastOff($mac_address);
    $on_time = strtotime($last_on['created']);
    $off_time = strtotime($last_off['created']);
    $time = $on_time - $off_time;
    //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
    return $time;
}
function WeMoOnNowTime($mac_address){
    /*
    $last_on = WeMoLogLastOn($mac_address);
    $last_off = WeMoLogLastOff($mac_address);
    $on_time = strtotime($last_on['created']);
    $off_time = strtotime($last_off['created']);
    */
    $time = WeMoOnNowTimeCalculate($mac_address);
    //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
    if($time < 0) $time = 0;
    return $time;
}
function WeMoOffNowTime($mac_address){
    /*
    $last_on = WeMoLogLastOn($mac_address);
    $last_off = WeMoLogLastOff($mac_address);
    $on_time = strtotime($last_on['created']);
    $off_time = strtotime($last_off['created']);
    */
    $time = WeMoOnNowTimeCalculate($mac_address) * -1;
    //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
    if($time < 0) $time = 0;
    return $time;
}
function WeMoOnNowMinutes($mac_address){
    return floor(WeMoOnNowTime($mac_address) / 60);
}
function WeMoOffNowMinutes($mac_address){
    return floor(WeMoOffNowTime($mac_address) / 60);
}
function WeMoOnNowHours($mac_address){
    return floor(WeMoOnNowTime($mac_address) / 60 / 60);
}
function WeMoOffNowHours($mac_address){
    return floor(WeMoOffNowTime($mac_address) / 60 / 60);
}
function WeMoOnNowDays($mac_address){
    return floor(WeMoOnNowTime($mac_address) / 60 / 60 / 24);
}
function WeMoOffNowDays($mac_address){
    return floor(WeMoOffNowTime($mac_address) / 60 / 60 / 24);
}
//
// how long an individual light has been off within a recent time window
//
function WeMoOffTime($mac_address,$seconds){
    $log = WeMoLogs::Recent($mac_address,$seconds);
    if(count($log) == 0)
        return 0;
    $start_time = strtotime($log[0]['created']);
    $start_off = $log[0]['state'];
    $off_time = 0;
    for($i = 1; $i < count($log); $i++){
        $end_time = strtotime($log[$i]['created']);
        $end_off = $log[$i]['state'];
        $time_diff = $end_time - $start_time;
        if($start_off == 0 || $start_off == "0"){
            $off_time += $time_diff;
        }
        $start_time = $end_time;
        $start_off = $end_off;
    }
    return $off_time;
}















//
// how long all the lights in a room have been off within a recent time window
//
function RoomOffTime($room_id,$seconds){
    $lights = WeMoLights::RoomLights($room_id);
    $lights_off = $seconds;
    foreach($lights as $light){
        $off_time = WeMoOffTime($light['mac_address'],$seconds);
        if($off_time < $lights_off)
            $lights_off = $off_time;    
    }
    return $lights_off;
}
//
// how long all the lights in a neighboring room have been off within a recent time window
//
function NeighborsOffTime($room_id,$seconds){
    $room_ids = RoomNeighbors::Neighbors($room_id);
    $lights_off = $seconds;
    foreach($room_ids as $room){
        $off_time = RoomOffTime($room,$seconds);
        if($off_time < $lights_off)
            $lights_off = $off_time;
    }
    return $lights_off;
}
//
// percentage of recent time window the lights in a neighboring room have been off
//
function NeighborsOffTimePercent($room_id,$seconds){
    $off_time = NeighborsOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
//
// percentage of recent time window the lights have been off in a room
//
function RoomOffTimePercent($room_id,$seconds){
    $off_time = RoomOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
//
// time a room's lamp has been off within a recent time window
//
function RoomLampOffTime($room_id,$seconds){
    $lights = WeMoLights::RoomLights($room_id,"lamp");
    $lights_off = $seconds;
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'LAMP') > -1){
            $off_time = WeMoOffTime($light['mac_address'],$seconds);
            if($off_time < $lights_off)
                $lights_off = $off_time;
        }
    }
    return $lights_off;
}
//
// percentage of recent time window room's lamp has been off
//
function RoomLampOffTimePercent($room_id,$seconds){
    $off_time = RoomLampOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
//
// time a room's mood lights has been off within a recent time window
//
function RoomMoodOffTime($room_id,$seconds){
    $lights = WeMoLights::RoomLights($room_id,"mood");
    $lights_off = $seconds;
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'MOOD') > -1){
            $off_time = WeMoOffTime($light['mac_address'],$seconds);
            if($off_time < $lights_off)
                $lights_off = $off_time;
        }
    }
    return $lights_off;
}
//
// percentage of recent time window room's mood lights has been off
//
function RoomMoodOffTimePercent($room_id,$seconds){
    $off_time = RoomMoodOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}

//
// time a room's lamp has been off within a recent time window
//
function RoomLampOnTime($room_id,$seconds){
    $lights = WeMoLights::RoomLights($room_id,"lamp");
    $lights_off = $seconds;
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'LAMP') > -1){
            $on_time = WeMoOnTime($light['mac_address'],$seconds);
            if($on_time < $lights_off)
                $lights_on = $on_time;
        }
    }
    return $lights_on;
}
//
// percentage of recent time window room's lamp has been off
//
function RoomLampOnTimePercent($room_id,$seconds){
    $on_time = RoomLampOnTime($room_id,$seconds);
    $percent = $on_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
//
// time a room's mood lights has been off within a recent time window
//
function RoomMoodOnTime($room_id,$seconds){
    $lights = WeMoLights::RoomLights($room_id,"mood");
    $lights_on = $seconds;
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'MOOD') > -1){
            $on_time = WeMoOnTime($light['mac_address'],$seconds);
            if($on_time < $lights_on)
                $lights_on = $on_time;
        }
    }
    return $lights_on;
}
//
// percentage of recent time window room's mood lights has been off
//
function RoomMoodOnTimePercent($room_id,$seconds){
    $on_time = RoomMoodOnTime($room_id,$seconds);
    $percent = $on_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}

//
// room lamp is on
//
function RoomLampIsOn($room_id){
    $lights = WeMoLights::RoomLights($room_id,"lamp");
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'LAMP') > -1){
            if($light['state'] != 0){
                return true;
            }
        }
    }
    return false;
}
//
// room mood is on
//
function RoomMoodIsOn($room_id){
    $lights = WeMoLights::RoomLights($room_id,"mood");
    foreach($lights as $light){
        if(strpos(strtoupper($light['name']),'MOOD') > -1){
            if($light['state'] != 0){
                return true;
            }
        }
    }
    return false;
}

//
// wemo error percent
//
function WeMoErrorPercent($mac_address,$days = 14){
    $logs = WeMoLogs::Recent($mac_address,DaysToSeconds($days));
    $errors = 0;
    foreach($logs as $log){
        if((int)$log['state'] == 2){
            $errors++;
        }
    }
    return ($errors / count($logs));
}
?>