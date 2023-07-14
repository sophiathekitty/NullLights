<?php
/**
 * handle syncing and observing tuya states
 */
class TuyaSync {
    /**
     * do observation if main hub
     * or sync from main hub (if main hub isn't old hub)
     * if it is the main hub is old hub do the observation
     */
    public static function Observe(){
        if(TuyaSync::DoObserve()) Tuya::Observe();
        else TuyaSync::Sync();
    }
    /**
     * do discovery if main hub
     * or if the hub is the old_hub
     */
    public static function Discover(){
        if(TuyaSync::DoObserve()) Tuya::Discover();
    }
    /**
     * see if we need to sync of do it locally
     */
    private static function DoObserve(){
        if(Servers::IsMain()) return true;
        $hub = Servers::GetHub();
        if($hub['type'] == "old_hub") return true;
        return false;
    }
    /**
     * Sync tuya from main hub
     */
    private static function Sync(){
        $tuyas = ServerRequests::LoadHubJSON("plugins/NullLights/api/tuya");
        if(!isset($tuyas['tuyas'])) return null; // quick escape if tuyas is missing
        foreach($tuyas['tuyas'] as $tuya){
            TuyaLights::SaveTuya($tuya);
        }
    }
}
?>