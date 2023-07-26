<?php
/**
 * a module for getting light profile stuff in a useful way
 * and doing fun stuff like applying lighting profiles
 */
class LightProfile {
    /**
     * get current lighting profile for a room
     * @param int $room_id the room id
     */
    public static function CurrentRoomProfile($room_id){
        $active = ActiveLightProfiles::RoomActiveProfile($room_id);
        $profile = LightingProfile::LightProfileId($active['profile_id']);
        if(is_null($profile)) return ['id'=>0,'name'=>'None','type'=>'night', 'light_level'=>0, 'light_level_min'=>null,'light_level_max'=>null,'devices'=>[]];
        $profile['devices'] = LightProfile::ProfileDevices($profile['id']);
        return $profile;
    }
    /**
     * get the devices and whether they're turn on or keep on
     * @param int $profile_id the id of the lighting profile
     */
    public static function ProfileDevices($profile_id){
        $devices = LightProfileDevice::LightProfileId($profile_id);
        foreach($devices as &$device){
            $light = RoomLightsGroup::LightId($device['light_id']);
            $device['name'] = $light['name'];
            $device['mode_txt'] = "Leave On";
            if(!is_null($device['state'])){
                if((int)$device['state'] == 1) $device['mode_txt'] = "Turn On";
                else $device['mode_txt'] = "Turn Off";
            } 
        }
        return $devices;
    }
    /**
     * apply lighting profiles
     */
    public static function ApplyLightingProfiles(){
        if(!LightProfile::DoApply()) return null;
        // get the profile devices for the active lighting profiles
        $profile_devices = ActiveLightProfiles::AllActiveLightProfileDevices();
        if(defined("PROFILE_SERVICE")) Services::Log(constant("PROFILE_SERVICES"),"LightProfile::ApplyLightingProfiles profile_devices:count:".count($profile_devices));
        $lights = [];
        foreach($profile_devices as &$device){
            // get the light group and set any target states
            $lights[$device['light_id']] = RoomLightsGroup::LightId($device['light_id']);
            if(defined("PROFILE_SERVICE")) Services::Log(constant("PROFILE_SERVICES"),"LightProfile::ApplyLightingProfiles device:light_id:".$device['light_id']);
            // set that this light has been controlled by a profile
            $lights[$device['light_id']]['last_profile_on'] = Date("Y-m-d H:i:s");
            // figure out target states and whatnot
            if(!is_null($device['state'])) $lights[$device['light_id']]['target_state'] = $device['state'];
            if(!is_null($device['percent'])) $lights[$device['light_id']]['target_percent'] = $device['percent'];
            if(!is_null($device['color'])) $lights[$device['light_id']]['target_color'] = $device['color'];
        }
        foreach($lights as $light_id => $light){
            // set the light state if the target state isn't -1
            if(defined("PROFILE_SERVICE")) Services::Log(constant("PROFILE_SERVICES"),"LightProfile::ApplyLightingProfiles light:name:".$light['name']);
            if(defined("PROFILE_SERVICE")) Services::Log(constant("PROFILE_SERVICES"),"LightProfile::ApplyLightingProfiles light:target_state:".$light['target_state']);
            if((int)$light['target_state'] != -1) LightGroups::SetState($light_id,$light['target_state']);
        }
    }
    /**
     * figure out if this device should try to apply lighting profiles to actual lights
     * @return bool returns true if this is the main hub or if the main hub is the old hub and this is the dev pi
     */
    private static function DoApply(){
        $main = Servers::GetMain();
        return (Servers::IsMain() || ($main['type'] == 'old_hub' && Settings::LoadSettingsVar("dev") == "dev"));
    }
    /**
     * set the active lighting profiles based on the room use
     */
    public static function SetLightingProfilesFromRoomUse(){
        if(defined("PROFILE_SERVICE")) Services::Log("NullLights::EveryMinute",constant("LIGHT_PROFILE_SERVICES")." hello?");//constant("DEVICE_GROUP_SERVICE")
        if(!HasPlugin("NullProfiles")) {throw new Exception("Missing NullProfiles"); return;}
        $rooms = Rooms::ActiveRooms();
        foreach($rooms as $room){
            $room_use = CurrentRoomUse($room['id']);
            if(is_null($room_use)){
                ActiveLightProfiles::ClearRoomLightingProfile($room['id']);
                continue;
            }
            if(defined("PROFILE_SERVICE")) Services::Log("NullLights::EveryMinute","LightProfile::SetLightingProfilesFromRoomUse roomUse:".$room_use['type']);
            $current_profile = LightProfile::CurrentRoomProfile($room['id']);
            if($current_profile['name'] != "None" && LightProfile::DoesProfileWorkForRoomUse($room_use,$current_profile)) {Services::Log("NullLights::EveryMinute","LightProfile::SetLightingProfilesFromRoomUse current:".$current_profile['name']); continue;}
            $profiles = LightingProfile::RoomId($room['id']);
            Debug::Log("LightProfile::SetLightingProfilesFromRoomUse",$room_use,$profiles);
            $clear = true;
            foreach($profiles as $profile){
                if(LightProfile::DoesProfileWorkForRoomUse($room_use,$profile)){
                    // apply this one
                    ActiveLightProfiles::ClearRoomLightingProfile($room['id']);
                    $save = ActiveLightProfiles::ApplyLightingProfile($profile);
                    if(defined("PROFILE_SERVICE")) Services::Log("NullLights::EveryMinute","LightProfile::SetLightingProfilesFromRoomUse apply:".$profile['name']);
                    if($save['error'] != "") Services::Error("NullLights::EveryMinute","LightProfile::SetLightingProfilesFromRoomUse error:".$save['error']);
                    $clear = false;
                    break;
                } else {
                    if(defined("PROFILE_SERVICE")) Services::Log("NullLights::EveryMinute","LightProfile::SetLightingProfilesFromRoomUse nope:".$profile['name']);
                }
            }
            if($clear) ActiveLightProfiles::ClearRoomLightingProfile($room['id']);
        }
    }
    /**
     * figure out if a profile satisfies a room use's light level request
     * @param array $room_use room use data array
     * @param array $profile lighting profile data array
     */
    public static function DoesProfileWorkForRoomUse($room_use,$profile){
        if(defined("PROFILE_SERVICE")) Services::Log("NullLights::EveryMinute","LightProfile::DoesProfileWorkForRoomUse ".$profile['name']);
        $target_light_level = (float)$room_use['light_level'];
        $min_light_level = (float)$profile['light_level_min'];
        $max_light_level = (float)$profile['light_level_max'];
        if($min_light_level < $target_light_level && $target_light_level <= $max_light_level) Services::Log("NullLights::EveryMinute","LightProfile::DoesProfileWorkForRoomUse ($min_light_level <  $target_light_level <= $max_light_level) true");
        else Services::Log("NullLights::EveryMinute","LightProfile::DoesProfileWorkForRoomUse ($min_light_level <  $target_light_level <= $max_light_level) false");
        return ($min_light_level < $target_light_level && $target_light_level <= $max_light_level);
    }
}
?>