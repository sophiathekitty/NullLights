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
}
?>