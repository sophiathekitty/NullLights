<?php
/**
 * Make sure that all the wemo's have a light group wrapper
 */
function FindWemoLightGroups(){
    LightGroups::FindWemoLightGroups();
    /*
    $wemos = WeMoLights::UnGrouped();
    foreach($wemos as $wemo){
        FindWemoGroup($wemo);
    }
    */
}
/**
 * find the group for a wemo
 * @param array $wemo the wemo data array
 * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
 */
function FindWemoGroup($wemo){
    return LightGroups::FindWemoGroup($wemo);
    /*
    $save = RoomLightsGroup::GroupWemo($wemo);
    Debug::Log("FindWemoLightGroup",$wemo,$save);
    return $save;
    */
}
class LightGroups {
    /**
     * apply the state for a light group
     * @param int $light_id the id for the RoomLightGroup
     * @param int $state the binary state for the lights/devices
     */
    public static function SetState($light_id,$state){
        $group = RoomLightsGroup::LightId($light_id);
        $group['target_state'] = $state;
        RoomLightsGroup::SaveLight($group);
        $wemos = WeMoLights::LightGroup($light_id);
        $success = true;
        foreach($wemos as $wemo){
            $wemo['target_state'] = $state;
            WeMoLights::SaveWeMo($wemo);
            WeMo::SetState($wemo);
            $wemo = WeMoLights::MacAddress($wemo['mac_address']);
            if($wemo['target_state'] != -1 || $wemo['state'] != $state) $success = false;
        }
        if(!Tuya::SetGroupState($light_id,$state)) $success = false;
        $govees = GoveeLights::LightGroup($light_id);
        foreach($govees as $govee){
            $govee['target_state'] = $state;
            GoveeLights::SaveGovee($govee);
            Govee::SetState($govee);
            $govee = GoveeLights::MacAddress($govee['mac_address']);
            if($govee['target_state'] != -1 || $govee['state'] != $state) $success = false;
        }
        // go through the tuya devices
        if($success){
            $group['target_state'] = -1;
            $group['state'] = $state;
            RoomLightsGroup::SaveLight($group);
        }
    }
    /**
     * Make sure that all the wemo's have a light group wrapper
     */
    public static function FindWemoLightGroups(){
        $wemos = WeMoLights::UnGrouped();
        foreach($wemos as $wemo){
            LightGroups::FindWemoGroup($wemo);
        }
    }
    /**
     * find the group for a wemo
     * @param array $wemo the wemo data array
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function FindWemoGroup($wemo){
        $save = RoomLightsGroup::GroupWemo($wemo);
        Debug::Log("FindWemoLightGroup",$wemo,$save);
        return $save;
    }
    /**
     * get light group state from light group members
     * (call this after observing individual wemo states)
     */
    public static function SyncStatesFromMembers(){
        define("DEBUGGING_LIGHT_GROUP_SYNC",true);
        Services::Start("LightGroups::SyncStates");
        $lights = RoomLightsGroup::AllLights();
        foreach($lights as $light){
            $light['state'] = 0; // default to off
            $light['error'] = 0; // assume no error
            $light['target_state'] = -1; // assume no error
            LightGroups::GetStateFromWemoMembers($light);
            LightGroups::GetStateFromTuyaMembers($light);
            LightGroups::GetStateFromGoveeMembers($light);
            $save = RoomLightsGroup::SaveLight($light);
            Services::Log("LightGroups::SyncStates","save error: ".$save['error']);
            RoomLightLogs::SaveLog($light);
        }
        Services::Complete("LightGroups::SyncStates");
    }
    /**
     * get light group state from light group members
     * (call this after observing individual wemo states)
     * @param array $light the light group object as from RoomLightsGroup::LightId($light_id);
     */
    public static function GetStateFromWemoMembers(&$light){
        Services::Log("LightGroups::SyncStates","WemoMembers:".$light['name']);
        $wemos = WeMoLights::LightGroup($light['id']);
        Services::Log("LightGroups::SyncStates","wemos: ".count($wemos));
        if(count($wemos) == 0) return;
        foreach($wemos as $wemo){
            Services::Log("LightGroups::SyncStates","wemo: ".$wemo['name']." [".$wemo['state']."] [".$wemo['error']."]");
            if((int)$wemo['state'] == 1) $light['state'] = 1;
            if((int)$wemo['state'] == 2) $light['state'] = 2;
            if((int)$wemo['target_state'] != -1) $light['target_state'] = (int)$wemo['target_state'];
            if((int)$wemo['error'] == 1) $light['error'] = 1;
        }
        Services::Log("LightGroups::SyncStates","state: ".$light['state']);
        Services::Log("LightGroups::SyncStates","error: ".$light['error']);
        //RoomLightLogs::SaveLog($light);
        //Services::Log("LightGroups::SyncStates","log guid: ".$save['row']['guid']);
    }
    /**
     * get light group state from light group members
     * (call this after observing individual wemo states)
     * @param array $light the light group object as from RoomLightsGroup::LightId($light_id);
     */
    public static function GetStateFromGoveeMembers(&$light){
        Services::Log("LightGroups::SyncStates","GoveeMembers:".$light['name']);
        $govees = GoveeLights::LightGroup($light['id']);
        Services::Log("LightGroups::SyncStates","govee: ".count($govees));
        if(count($govees) == 0) return;
        foreach($govees as $govee){
            Services::Log("LightGroups::SyncStates","govee: ".$govee['name']." [".$govee['state']."] [".$govee['error']."]");
            if((int)$govee['state'] == 1) $light['state'] = 1;
            if((int)$govee['state'] == 2) $light['state'] = 2;
            if((int)$govee['target_state'] != -1) $light['target_state'] = (int)$govee['target_state'];
            if((int)$govee['error'] == 1) $light['error'] = 1;
        }
        Services::Log("LightGroups::SyncStates","state: ".$light['state']);
        Services::Log("LightGroups::SyncStates","error: ".$light['error']);
        //RoomLightLogs::SaveLog($light);
        //Services::Log("LightGroups::SyncStates","log guid: ".$save['row']['guid']);
    }
    /**
     * get light group state from light group members
     * (call this after observing individual tuya states)
     * @param array $light the light group object as from RoomLightsGroup::LightId($light_id);
     */
    public static function GetStateFromTuyaMembers(&$light){
        Services::Log("LightGroups::SyncStates","TuyaMembers:".$light['name']);
        $tuyas = TuyaLights::LightGroup($light['id']);
        Services::Log("LightGroups::SyncStates","tuya: ".count($tuyas));
        if(count($tuyas) == 0) return;
        foreach($tuyas as $tuya){
            Services::Log("LightGroups::SyncStates","tuya: ".$tuya['name']." [".$tuya['state']."] [".$tuya['error']."]");
            if((int)$tuya['state'] == 1) $light['state'] = 1;
            if((int)$tuya['state'] == 2) $light['state'] = 2;
            if((int)$tuya['target_state'] != -1) $light['target_state'] = (int)$tuya['target_state'];
            if((int)$tuya['error'] == 1) $light['error'] = 1;
        }
        Services::Log("LightGroups::SyncStates","state: ".$light['state']);
        Services::Log("LightGroups::SyncStates","error: ".$light['error']);
        //RoomLightLogs::SaveLog($light);
        //Services::Log("LightGroups::SyncStates","log guid: ".$save['row']['guid']);
    }
    /**
     * Log room lights group
     */
    public static function Log(){
        $groups = RoomLightsGroup::AllLights();
        foreach($groups as $group){
            RoomLightLogs::SaveLog($group);
        }
    }
    /**
     * Basic Automate pass for RoomLightGroup. this handles the basic snooze and max on functions
     */
    public  static function BasicAutomation(&$roomLights = null)
    {
        // Load all RoomLights if they weren't sent
        $save = false;
        if(is_null($roomLights)){
            // no RoomLights were sent so this is running stand alone and need to save if the changes are going to go anywhere
            $roomLights = RoomLightsGroup::AllLights();
            $save = true;
        }
        $currentTime = date('Y-m-d H:i:s');
        // Update and save each individual RoomLight
        foreach ($roomLights as &$roomLight) {
            // check mode to see if the light should be on or off
            if ($roomLight['mode'] == "on") {
                $roomLight['target_state'] = 1;
            } elseif ($roomLight['mode'] == "off") {
                $roomLight['target_state'] = 0;
            }
            // if the light isn't automated don't automate
            if ($roomLight['mode'] != "auto") continue; 
            // Update last_on and last_off timestamps based on the current state
            if ($roomLight['state'] == 1) {
                $roomLight['last_on'] = $currentTime;
            } else {
                $roomLight['last_off'] = $currentTime;
            }
            // Check snooze duration if snooze_minutes is set
            if (isset($roomLight['snooze_minutes']) && $roomLight['snooze_minutes'] !== null && (int)$roomLight['state'] == 1 && $roomLight['snooze_minutes'] > 0) {
                $lastOffForSnooze = $roomLight['last_off'];
                // Override last_off for snoozing calculations
                if (!is_null($roomLight['last_profile_on']) && strtotime($roomLight['last_profile_on']) > strtotime($lastOffForSnooze)) {
                    $lastOffForSnooze = $roomLight['last_profile_on'];
                }
                $snoozeDuration = strtotime($currentTime) - strtotime($lastOffForSnooze);
                if ($snoozeDuration >= MinutesToSeconds($roomLight['snooze_minutes'])) {
                    // Snooze the light
                    $roomLight['target_state'] = 0;
                }
            }
            // Calculate maximum on duration if max_on_hours is set
            if (isset($roomLight['max_on_hours']) && $roomLight['max_on_hours'] !== null && $roomLight['max_on_hours'] > 0) {
                $maxOnDuration = $roomLight['max_on_hours'] * 3600;
                $restDuration = 0.75 * $maxOnDuration;
                $windowDuration = $maxOnDuration + $restDuration;
                $onTimeWithinWindow = RoomLightLogs::Recent($roomLight['id'],$windowDuration);
                if ($onTimeWithinWindow >= $maxOnDuration) {
                    // Rest period required, set target_state to off
                    $roomLight['target_state'] = 0;
                }
            }
            if($save) RoomLightsGroup::SaveLight($roomLight);
        }
    }
}
?>