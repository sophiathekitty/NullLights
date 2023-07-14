<?php
/**
 * Tuya module
 */
class Tuya {
    /**
     * Discover Service:
     * this will discover the tuya devices on the network
     * and attempt to add them to room lights groups.
     */
    public static function Discover(){
        Services::Start("NullLights::Tuya::Discover");
        Services::Log("NullLights::Tuya::Discover","run python3 tuyaDiscover.py");
        shell_exec("python3 /var/www/html/plugins/NullLights/python/tuyaDiscover.py");
        Services::Log("NullLights::Tuya::Discover","tuyaDiscover.py done");
        $tuyas = TuyaLights::AllLights();
        foreach($tuyas as $tuya){
            Services::Log("NullLights::Tuya::Discover","Group: ".$tuya['name']);
            RoomLightsGroup::GroupTuya($tuya);
        }
        Services::Complete("NullLights::Tuya::Discover");
    }
    /**
     * Check Tuya Status:
     * go through the tuya and get their status
     * and attempt to apply any target states
     */
    public static function Observe(){
        Services::Start("NullLights::Tuya::Observe");
        Services::Log("NullLights::Tuya::Observe","run python3 tuyaObserve.py");
        shell_exec("python3 /var/www/html/plugins/NullLights/python/tuyaObserve.py");
        Services::Log("NullLights::Tuya::Observe","tuyaObserve.py done");
        Services::Complete("NullLights::Tuya::Observe");
    }
    /**
     * Set Tuya Group State:
     * set the state of a room light group's tuyas
     * @param int $light_id the room lights group id
     * @param int $state the state (1 for on, 0 for off)
     * @return bool false if failed
     */
    public static function SetGroupState($light_id,$state){
        $tuyas = TuyaLights::LightGroup($light_id);
        if(count($tuyas) == 0) return true; // no tuyas so it didn't fail
        if(defined("DEBUG")){
            $res = shell_exec("python3 /var/www/html/plugins/NullLights/python/tuyaSetGroupState.py $light_id $state");
            Debug::Log($res);
        } else shell_exec("python3 /var/www/html/plugins/NullLights/python/tuyaSetGroupState.py $light_id $state");
        $tuyas = TuyaLights::LightGroup($light_id);
        foreach($tuyas as $tuya){
            if($tuya['state'] != $state) return false;
        }
        return true; // states must have all matched
    }
}
?>