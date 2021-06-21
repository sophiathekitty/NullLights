<?php
class AutomationRuleHandler {
    public static $commands = ["OnDuringTime", "LightOnTime","LightOffTime","LightOnMinutes","LightOffMinutes","LightOnHours","LightOffHours","LightOnDays", "LightOffDays", "RoomOnTime", "RoomOffTime", "NeighborOnTime", "NeighborOffTime", "RoomLampOnTime", "RoomLampOffTime", "RoomMoodOnTime", "RoomMoodOffTime", "IsDayInside", "IsDayInRoom","IsTimeForBed", "IsBedtimeHours", "IsTimeToGetUp", "IsMorning","IsEvening","IsDaytime","IsNighttime","IsMorningRoom","IsEveningRoom","IsDaytimeRoom","IsNighttimeRoom","IsRaining","IsWarmerOutside","IsCoolerOutside", "IsCoolEnough"];
    /**
     * converts conditions string into a json array
     * @param string $conditions
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
     * @return bool returns true if condition is true
     */
    public static function EvaluateCondition($wemo, $condition){
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
                
            case "IsDayInRoom":
            case "IsTimeForBed":
            case "IsBedtimeHours":
            case "IsTimeToGetUp":
            case "IsMorning":
            case "IsEvening":
            case "IsDaytime":
            case "IsNighttime":
            case "IsMorningRoom":
            case "IsEveningRoom":
            case "IsDaytimeRoom":
            case "IsNighttimeRoom":
            case "IsRaining":
            case "IsWarmerOutside":
            case "IsCoolerOutside":
            case "IsCoolEnough":
        }
        return false;
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
                
                
            }
            if($pass){
                // do automation
            }
        }
    }
}














?>