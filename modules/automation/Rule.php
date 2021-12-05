<?php
class AutomationRuleHandler {
    /**
     * converts conditions string into a json array
     * @param string $conditions cmd cmd:val cmd:val1,val2,val3
     * @return array json array of conditions
     */
    public static function ParseConditions($conditions){
        $cons = explode(" ",$conditions);
        $cond = [];
        foreach($cons as $con){
            $check = [];
            if(strpos($con,":") > -1){
                list($check['cmd'],$val) = explode(":",$con);
                if(strpos($val,",") > -1){
                    $check['vals'] = explode(",",$val);
                } else {
                    $check['val'] = $val;
                }
            } else {
                $check['cmd'] = $con;
            }
            $cond[] = $check;
        }
        return $cond;
    }
    /**
     * Evaluates the condition for the wemo
     * @param array $wemo json array for a wemo
     * @param array $condition parsed condition
     * @return array returns ["pass"=>true] if condition is true
     */
    public static function EvaluateCondition($wemo, $condition):array{
        /**
         * standard automation conditions
         */
        switch($condition['cmd']){
            case "OnDuringTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::OnDuringTime($wemo,$condition['vals'][1]) > (float)$condition['vals'][0]),"val"=>WemoTime::OnDuringTime($wemo,$condition['vals'][1])];
                //return (isset($condition['val']) && WemoTime::OnDuringTime($wemo,$condition['vals'][1]) > (float)$condition['vals'][0]);
            case "LightOnTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::OnTime($wemo) > (float)$condition['val']),"val"=>WemoTime::OnTime($wemo)];
                //return (isset($condition['val']) && WemoTime::OnTime($wemo) > (float)$condition['val']);
            case "LightOffTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::OffTime($wemo) > (float)$condition['val']),"val"=>WemoTime::OffTime($wemo)];
                //return (isset($condition['val']) && WemoTime::OffTime($wemo) > (float)$condition['val']);
            case "LightOnMinutes":
                return ["pass"=>(isset($condition['val']) && WemoTime::OnMinutes($wemo) > (float)$condition['val']),"val"=>WemoTime::OnMinutes($wemo)];
                //return (isset($condition['val']) && WemoTime::OnMinutes($wemo) > (float)$condition['val']);
            case "LightOffMinutes":
                return ["pass"=>(isset($condition['val']) && WemoTime::OffMinutes($wemo) > (float)$condition['val']),"val"=>WemoTime::OffMinutes($wemo)];
                //return (isset($condition['val']) && WemoTime::OffMinutes($wemo) > (float)$condition['val']);
            case "LightOnHours":
                return ["pass"=>(isset($condition['val']) && WemoTime::OnHours($wemo) > (float)$condition['val']),"val"=>WemoTime::OnHours($wemo)];
                //return (isset($condition['val']) && WemoTime::OnHours($wemo) > (float)$condition['val']);
            case "LightOffHours":
                return ["pass"=>(isset($condition['val']) && WemoTime::OffHours($wemo) > (float)$condition['val']),"val"=>WemoTime::OffHours($wemo)];
                //return (isset($condition['val']) && WemoTime::OffHours($wemo) > (float)$condition['val']);
            case "LightOnDays":
                return ["pass"=>(isset($condition['val']) && WemoTime::OnDays($wemo) > (float)$condition['val']),"val"=>WemoTime::OnDays($wemo)];
                //return (isset($condition['val']) && WemoTime::OnDays($wemo) > (float)$condition['val']);
            case "LightOffDays":
                return ["pass"=>(isset($condition['val']) && WemoTime::OffDays($wemo) > (float)$condition['val']),"val"=>WemoTime::OffDays($wemo)];
                //return (isset($condition['val']) && WemoTime::OffDays($wemo) > (float)$condition['val']);
            case "RoomOnTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomOnTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomOnTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomOnTime($wemo) > (float)$condition['val']);
            case "RoomOffTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomOffTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomOffTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomOffTime($wemo) > (float)$condition['val']);
            case "NeighborOnTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::NeighborsOnTime($wemo) > (float)$condition['val']),"val"=>WemoTime::NeighborsOnTime($wemo)];
                //return (isset($condition['val']) && WemoTime::NeighborsOnTime($wemo) > (float)$condition['val']);
            case "NeighborOffTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::NeighborsOffTime($wemo) > (float)$condition['val']),"val"=>WemoTime::NeighborsOffTime($wemo)];
                //return (isset($condition['val']) && WemoTime::NeighborsOffTime($wemo) > (float)$condition['val']);
            case "RoomLampOnTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomLampOnTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomLampOnTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomLampOnTime($wemo) > (float)$condition['val']);
            case "RoomLampOffTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomLampOffTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomLampOffTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomLampOffTime($wemo) > (float)$condition['val']);
            case "RoomMoodOnTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomMoodOnTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomMoodOnTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomMoodOnTime($wemo) > (float)$condition['val']);
            case "RoomMoodOffTime":
                return ["pass"=>(isset($condition['val']) && WemoTime::RoomMoodOffTime($wemo) > (float)$condition['val']),"val"=>WemoTime::RoomMoodOffTime($wemo)];
                //return (isset($condition['val']) && WemoTime::RoomMoodOffTime($wemo) > (float)$condition['val']);
            case "IsDayInside":
                return ["pass"=>(TimeOfDay::IsDayInside())];
            case "IsNightInside":
                return ["pass"=>(!TimeOfDay::IsDayInside())];
            case "IsDayInRoom":
                return ["pass"=>(TimeOfDay::IsDayInRoom($wemo['room_id']))];
            case "IsNightInRoom":
                return ["pass"=>(!TimeOfDay::IsDayInRoom($wemo['room_id']))];
            case "IsEveningInRoom":
                return ["pass"=>(TimeOfDay::IsEveningInRoom($wemo['room_id']))];
            case "IsBedtimeHours":
                return ["pass"=>(!TimeOfDay::IsDayInRoom($wemo['room_id']))];
            case "IsMorningInRoom":
                return ["pass"=>(TimeOfDay::IsMorningInRoom($wemo['room_id']))];
            case "IsMorning":
                return ["pass"=>(TimeOfDay::IsMorning())];
            case "IsEvening":
                return ["pass"=>(TimeOfDay::IsEvening())];
            case "IsDaytime":
                return ["pass"=>(TimeOfDay::IsDayTime())];
            case "IsNighttime":
                return ["pass"=>(!TimeOfDay::IsDayTime())];
        }
        /**
         * weather based automation
         */
        if(defined("WeatherPlugin")){
            switch($condition['cmd']){
                case "IsRainy":
                    return ["pass"=>(WeatherCondition::IsRainy())];
                case "IsRaining":
                    return ["pass"=>(WeatherCondition::IsRaining())];
                case "IsSnowy":
                    return ["pass"=>(WeatherCondition::IsSnowy())];
                case "IsSnowing":
                    return ["pass"=>(WeatherCondition::IsSnowing())];
                case "IsClear":
                    return ["pass"=>(WeatherCondition::IsClear())];
            }    
        }
        /**
         * see if sensor data ia above or below a threshold
         */
        if(defined("SensorsPlugin")){
            switch($condition['cmd']){
                case "RoomTemperatureAbove":
                    $temp = RoomDHT11::RoomTemperature($wemo['room_id']);
                    return ["pass"=>($temp ['temp'] > (float)$condition['val']),"val"=>$temp ['temp']];
                case "RoomTemperatureBelow":
                    $temp = RoomDHT11::RoomTemperature($wemo['room_id']);
                    return ["pass"=>($temp ['temp'] < (float)$condition['val']),"val"=>$temp ['temp']];
                case "RecentMotionDetected":
                case "LightAboveThreshold":
                case "LightBelowThreshold":
            }    
        }
        /**
         * for automating a window fan
         */
        if(defined("WeatherPlugin") && defined("SensorsPlugin")){
            switch($condition['cmd']){
                case "CoolerOutsideThanRoom":
                    $temp = RoomDHT11::RoomTemperature($wemo['room_id']);
                    $weather = WeatherLogs::CurrentWeather();
                    return ["pass"=>($temp ['temp'] > $weather['temp']),"val"=>($temp ['temp'] + ">" + $weather['temp'])];
                case "WarmerOutsideThanRoom":
                    $temp = RoomDHT11::RoomTemperature($wemo['room_id']);
                    $weather = WeatherLogs::CurrentWeather();
                    return ["pass"=>($temp ['temp'] < $weather['temp']),"val"=>($temp ['temp'] + "<" + $weather['temp'])];
            }    
        }
        return ["pass"=>false];
    }
    /**
     * apply a rule for a wemo
     */
    public static function ApplyRule($rule){
        $wemos = WeMoLights::RoomWeMos($rule['room_id'],$rule['type'],$rule['subtype']);
        $conditions = AutomationRuleHandler::ParseConditions($rule['conditions']);
        foreach($wemos as $wemo){
            $pass = true;
            $details = "";
            foreach($conditions as $condition){
                $check = AutomationRuleHandler::EvaluateCondition($wemo,$condition);
                if($details != "") $details .= " ";
                if(!$check['pass']){
                    $details .= "Not";
                    $pass = false;
                }
                $details .= $condition['cmd'];
                if(isset($check['val'])) $details .= ":".$check['val'];
            }
            if($pass){
                // do automation
                AutomationRuleHandler::ApplyAutomation($wemo,$rule,$details);
            }
        }
    }
    /**
     * apply the automation to the wemo
     */
    public static function ApplyAutomation($wemo,$rule,$details){
        if($wemo['state'] != $rule['state'] && $wemo['target_state'] != $rule['state'] && (SecondsToMinutes(AutomationLogs::TimeSinceAutomaticLightEvent($wemo,$rule['name'])) > 60 || $rule['state'] == 1)){
            AutomationLogs::SaveLog(["mac_address"=>$wemo['mac_address'],"event"=>$rule['name'],"details"=>$details]);
            WeMoLights::SaveWeMo(["mac_address"=>$wemo['mac_address'],"target_state"=>$rule['state']]);
            // run python script
            WeMo::Observe();
            WeMo::SetState($wemo);    
        }
    }
}














?>