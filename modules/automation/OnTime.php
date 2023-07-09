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
        Debug::Log("WemoTime::OnTimeCalculate",$last_on,$last_off,"$on_time - $off_time == $time",$wemo);
        //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
        return $time;            
    }
    /**
     * how long a light has been on consecutively
     * @param array $wemo WeMoLight data array
     * @return int time in seconds
     */
    public static function OnTime($wemo){
        $time = WemoTime::OnTimeCalculate($wemo);
        if($time < 0) $time = 0;
        return $time;            
    }
    /**
     * how long a light has been off consecutively
     * @param array $wemo WeMoLight data array
     * @return int time in seconds
     */
    public static function OffTime($wemo){
        $time = WemoTime::OnTimeCalculate($wemo) * -1;
        if($time < 0) $time = 0;
        return $time;                    
    }
    /**
     * how long a light has been on consecutively in minutes
     * @param array $wemo WeMoLight data array
     * @return float time in minutes
     */
    public static function OnMinutes($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToMinutes($time);
    }
    /**
     * how long a light has been off consecutively in minutes
     * @param array $wemo WeMoLight data array
     * @return float time in minutes
     */
    public static function OffMinutes($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToMinutes($time);
    }
    /**
     * how long a light has been on consecutively in hours
     * @param array $wemo WeMoLight data array
     * @return float time in hours
     */
    public static function OnHours($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToHours($time);
    }
    /**
     * how long a light has been off consecutively in hours
     * @param array $wemo WeMoLight data array
     * @return float time in hours
     */
    public static function OffHours($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToHours($time);
    }
    /**
     * how long a light has been on consecutively in days
     * @param array $wemo WeMoLight data array
     * @return float time in days
     */
    public static function OnDays($wemo){
        $time = WemoTime::OnTime($wemo);
        return SecondsToDays($time);
    }
    /**
     * how long a light has been off consecutively in days
     * @param array $wemo WeMoLight data array
     * @return float time in days
     */
    public static function OffDays($wemo){
        $time = WemoTime::OffTime($wemo);
        return SecondsToDays($time);
    }
    /**
     * the cumulative time a light has been on during a time window
     * @param array $wemo WeMoLight data array
     * @param array $time time window to check in seconds
     * @return {int|float} time in seconds
     */
    public static function OnDuringTime($wemo,$time){
        $log = WeMoLogs::Recent($wemo['mac_address'],$time);
        Debug::Log("WeMoTime::OnDuringTime",$wemo,$time,$log,clsDB::$db_g->last_sql,clsDB::$db_g->get_err());
        if(count($log) == 0)
            return 0;
        $start_time = strtotime($log[0]['created']);
        $start_on = $log[0]['state'];
        $on_time = 0;
        for($i = 1; $i < count($log); $i++){
            $end_time = strtotime($log[$i]['created']);
            $end_on = $log[$i]['state'];
            $time_diff = $end_time - $start_time;
            if((int)$start_on == 1){
                $on_time += $time_diff;
            }
            Debug::Log("WeMoTime::OnDuringTime",$wemo,"end_time:",$log[$i]['created'],"start_on: $start_on","end_on: $end_on","time_diff: $time_diff","on_time: $on_time");
            $start_time = $end_time;
            $start_on = $end_on;
        }
        return $on_time;    
    }
    /**
     * how long the lights have been on in room
     * @param array $wemo WeMoLight data array
     * @return float the time in seconds lights have been on in room
     */
    public static function RoomOnTime($wemo){
        return WemoTime::RoomOnTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lights have been on in room
     * @param int $room_id room id
     * @return float the time in seconds lights have been on in room
     */
    public static function RoomOnTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id);
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lights have been off in room
     * @param array $wemo WeMoLight data array
     */
    public static function RoomOffTime($wemo){
        return WemoTime::RoomOffTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lights have been off in room
     * @param int $room_id the room id
     */
    public static function RoomOffTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id);
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
     * @param array $wemo WeMoLight data array
     * 
     */
    public static function NeighborsOnTime($wemo){
        return WemoTime::NeighborsOnTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lights have been on in room
     * @param array $wemo WeMoLight data array
     * 
     */
    public static function NeighborsOnTime_RoomId($room_id){
        $neighbors = RoomNeighbors::Neighbors($room_id);
        $time = 0;
        Debug::Log("NeighborsOnTime",$room_id,$neighbors);
        foreach($neighbors as $neighbor){
            $id = $neighbor['neighbor_id'];
            if($id == $room_id && $neighbor['room_id'] != $room_id) $id = $neighbor['room_id'];
            $t = WemoTime::RoomOnTime_RoomId($id);
            Debug::Log("NeighborsOnTime - neighbor",$id,$t);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lights have been off in room
     * @param array $wemo WeMoLight data array
     */
    public static function NeighborsOffTime($wemo){
        return WemoTime::NeighborsOffTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lights have been off in room
     * @param array $wemo WeMoLight data array
     */
    public static function NeighborsOffTime_RoomId($room_id){
        $neighbors = RoomNeighbors::Neighbors($room_id);
        $time = null;
        Debug::Log("NeighborsOffTime",$room_id,$neighbors);
        foreach($neighbors as $neighbor){
            $id = $neighbor['neighbor_id'];
            if($id == $room_id && $neighbor['room_id'] != $room_id) $id = $neighbor['room_id'];
            $t = WemoTime::RoomOffTime_RoomId($id);
            if(is_null($time)) $time = $t;
            if($t < $time) $time = $t;
        }
        return $time;
    }
    /**
     * check if a room has a lamp on
     * @param array $wemo WeMoLight data array
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
     * @param array $wemo WeMoLight data array
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
     * @param array $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomLampOnTime($wemo){
        return WemoTime::RoomLampOnTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lamp has been on in a room
     * @param int $room_id room id
     * @return float time in seconds
     */
    public static function RoomLampOnTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id,"lamp");
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param array $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomLampOffTime($wemo){
        return WemoTime::RoomLampOffTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lamp has been on in a room
     * @param int $room_id room id
     * @return float time in seconds
     */
    public static function RoomLampOffTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id,"lamp");
        //Debug::Log("WeMoTime::RoomLampOffTime",$wemo,$lights);
        $time = null;
        foreach($lights as $light){
            $t = WemoTime::OffTime($light);
            if(is_null($time)) $time = $t;
            if($t < $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param array $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomMoodOnTime($wemo){
        return WemoTime::RoomMoodOnTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lamp has been on in a room
     * @param int $room_id room id
     * @return float time in seconds
     */
    public static function RoomMoodOnTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id,"mood");
        $time = 0;
        foreach($lights as $light){
            $t = WemoTime::OnTime($light);
            if($t > $time) $time = $t;
        }
        return $time;
    }
    /**
     * how long the lamp has been on in a room
     * @param array $wemo WeMoLight data array
     * @return float time in seconds
     */
    public static function RoomMoodOffTime($wemo){
        return WemoTime::RoomMoodOffTime_RoomId($wemo['room_id']);
    }
    /**
     * how long the lamp has been on in a room
     * @param int $room_id room id
     * @return float time in seconds
     */
    public static function RoomMoodOffTime_RoomId($room_id){
        $lights = WeMoLights::RoomLights($room_id,"mood");
        $time = null;
        foreach($lights as $light){
            $t = WemoTime::OffTime($light);
            if(is_null($time)) $time = $t;
            if($t < $time) $time = $t;
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














/**
 * how long an individual light has been on within a recent time window
 * @param string $mac_address the mac address of the wemo
 * @param int $seconds the seconds of the window to check back in time
 * @return int the seconds the light has been on within the time window
 * @depreciated use `WemoTime::OnDuringTime($wemo)`
 */
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
/**
 * on now time calculation gets the last on and last off times and 
 * @param string $mac_address the mac address of the wemo
 * @return int on_time - off_time
 * @use `WeMoOnNowTime($mac_address)` or `WeMoOffNowTime($mac_address)` functions for best results
 * @depreciated just don't use this....?
 */
function WeMoOnNowTimeCalculate($mac_address){
    $last_on = WeMoLogs::LastOn($mac_address);
    $last_off = WeMoLogs::LastOff($mac_address);
    $on_time = strtotime($last_on['created']);
    $off_time = strtotime($last_off['created']);
    $time = $on_time - $off_time;
    //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
    return $time;
}
/**
 * calculate how long wemo has been on
 * @param string $mac_address the mac address of the wemo
 * @return int seconds light has been on
 * @depreciated use `WemoTime::OnTime($wemo)` instead
 */
function WeMoOnNowTime($mac_address){
    $light = WeMoLights::MacAddress($mac_address);
    return WemoTime::OnTime($light);
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
/**
 * calculate how long wemo has been off
 * @param string $mac_address the mac address of the wemo
 * @return int seconds light has been off
 * @depreciated use `WemoTime::OffTime($wemo)` instead
 */
function WeMoOffNowTime($mac_address){
    $light = WeMoLights::MacAddress($mac_address);
    return WemoTime::OffTime($light);
    /*
    $last_on = WeMoLogLastOn($mac_address);
    $last_off = WeMoLogLastOff($mac_address);
    $on_time = strtotime($last_on['created']);
    $off_time = strtotime($last_off['created']);
    
    $time = WeMoOnNowTimeCalculate($mac_address) * -1;
    //echo "$last_on - $last_off == $on_time - $off_time == $time <br>";
    if($time < 0) $time = 0;
    return $time;
    */
}
/**
 * calculate how long wemo has been on
 * @param string $mac_address the mac address of the wemo
 * @return int minutes light has been on
 * @depreciated use `WemoTime::OnMinutes($wemo)` instead
 */
function WeMoOnNowMinutes($mac_address){
    return (WeMoOnNowTime($mac_address) / 60);
}
/**
 * calculate how long wemo has been off
 * @param string $mac_address the mac address of the wemo
 * @return int minutes light has been off
 * @depreciated use `WemoTime::OffMinutes($wemo)` instead
 */
function WeMoOffNowMinutes($mac_address){
    return (WeMoOffNowTime($mac_address) / 60);
}
/**
 * calculate how long wemo has been on
 * @param string $mac_address the mac address of the wemo
 * @return int hours light has been on
 * @depreciated use `WemoTime::OnHours($wemo)` instead
 */
function WeMoOnNowHours($mac_address){
    return (WeMoOnNowTime($mac_address) / 60 / 60);
}
/**
 * calculate how long wemo has been off
 * @param string $mac_address the mac address of the wemo
 * @return int hours light has been off
 * @depreciated use `WemoTime::OffHours($wemo)` instead
 */
function WeMoOffNowHours($mac_address){
    return (WeMoOffNowTime($mac_address) / 60 / 60);
}
/**
 * calculate how long wemo has been on
 * @param string $mac_address the mac address of the wemo
 * @return int days light has been on
 * @depreciated use `WemoTime::OnDays($wemo)` instead
 */
function WeMoOnNowDays($mac_address){
    return (WeMoOnNowTime($mac_address) / 60 / 60 / 24);
}
/**
 * calculate how long wemo has been off
 * @param string $mac_address the mac address of the wemo
 * @return int days light has been off
 * @depreciated use `WemoTime::OffDays($wemo)` instead
 */
function WeMoOffNowDays($mac_address){
    return (WeMoOffNowTime($mac_address) / 60 / 60 / 24);
}
/**
 * how long an individual light has been off within a recent time window
 * @param string $mac_address the mac address of the wemo
 * @param int $seconds the seconds of the window to check back in time
 * @return int the seconds the light has been off within the time window
 * @depreciated without a replacement
 */
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















/**
 * how long all the lights in a room have been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::RoomLampOffTime($wemo)`
 */
function RoomOffTime($room_id,$seconds){
    return WeMoTime::RoomOffTime_RoomId($room_id);
    /*
    $lights = WeMoLights::RoomLights($room_id);
    $lights_off = $seconds;
    foreach($lights as $light){
        $off_time = WeMoOffTime($light['mac_address'],$seconds);
        if($off_time < $lights_off)
            $lights_off = $off_time;    
    }
    return $lights_off;
    */
}
/**
 * how long all the lights in a neighboring room have been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::NeighborsOffTime($wemo)`
 */
function NeighborsOffTime($room_id,$seconds){
    return WemoTime::NeighborsOffTime_RoomId($room_id);
    $room_ids = RoomNeighbors::Neighbors($room_id);
    $lights_off = $seconds;
    foreach($room_ids as $room){
        $off_time = RoomOffTime($room,$seconds);
        if($off_time < $lights_off)
            $lights_off = $off_time;
    }
    return $lights_off;
}
/**
 * percentage of recent time window the lights in a neighboring room have been off
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 */
function NeighborsOffTimePercent($room_id,$seconds){
    $off_time = NeighborsOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
/**
 * percentage of recent time window the lights have been off in a room
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 */
function RoomOffTimePercent($room_id,$seconds){
    $off_time = RoomOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
/**
 * time a room's lamp has been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::RoomLampOffTime($wemo)`
 */
function RoomLampOffTime($room_id,$seconds){
    return WemoTime::RoomLampOffTime_RoomId($room_id);
    /*
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
    */
}
/**
 * percentage of recent time window room's lamp has been off
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 */
function RoomLampOffTimePercent($room_id,$seconds){
    $off_time = RoomLampOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
/**
 * time a room's mood lights has been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::RoomMoodOffTime($wemo)`
 */
function RoomMoodOffTime($room_id,$seconds){
    return WemoTime::RoomMoodOffTime_RoomId($room_id);
    /*
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
    */
}
/**
 * percentage of recent time window room's mood lights has been off
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 */
function RoomMoodOffTimePercent($room_id,$seconds){
    $off_time = RoomMoodOffTime($room_id,$seconds);
    $percent = $off_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
/**
 * time a room's lamp has been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::RoomLampOnTime($wemo)`
 */
function RoomLampOnTime($room_id,$seconds){
    return WemoTime::RoomLampOnTime_RoomId($room_id);
    /*
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
    */
}
/**
 * percentage of recent time window room's lamp has been off
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 */
function RoomLampOnTimePercent($room_id,$seconds){
    $on_time = RoomLampOnTime($room_id,$seconds);
    $percent = $on_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}
/**
 * time a room's mood lights has been off within a recent time window
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @depreciated use `WemoTime::RoomMoodOffTime($wemo)`
 */
function RoomMoodOnTime($room_id,$seconds){
    return WemoTime::RoomMoodOnTime_RoomId($room_id);
    /*
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
    */
}
/**
 * percentage of recent time window room's mood lights has been off
 * @param int $room_id the room's id
 * @param int $seconds seconds back in time for the time window
 * @return bool return true if room's mood light is on
 */
function RoomMoodOnTimePercent($room_id,$seconds){
    $on_time = RoomMoodOnTime($room_id,$seconds);
    $percent = $on_time/$seconds;
    if($percent > 0.79) $percent = 1;
    return $percent;
}

/**
 * room lamp is on
 * @param int $room_id the room's id
 * @return bool return true if room's lamp light is on
 * @depreciated use `WemoTime::RoomLampIsOn($wemo)`
 */
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
/**
 * room mood is on
 * @param int $room_id the room's id
 * @return bool return true if room's mood light is on
 * @depreciated use `WemoTime::RoomMoodIsOn($wemo)`
 */
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
/**
 * wemo error percent
 * @param string $mac_address the mac address of the wemo
 * @param int $days how many days back to look
 * @return float the percentage of the logs that were error
 * @warning looks like it's using state=2 instead of an error field?
 * probably doesn't actually work as intended anymore.
 */
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