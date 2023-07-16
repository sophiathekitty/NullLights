<?php
/**
 * handles doing the observing and syncing
 * based on whether or not this device is the
 * main hub
 */
class GoveeSync {
    /**
     * Discover Govee Devices
     */
    public static function Discover(){
        if(!GoveeSync::DoObserve()) return null;
        Govee::FindDevices();
    }
    /**
     * Observe or Sync Govee Devices
     */
    public static function Observe(){
        if(GoveeSync::DoObserve()){
            Govee::Observe();
        } else {
            GoveeSync::Sync();
        }
    }
    /**
     * Sync Govee
     */
    private static function Sync(){

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
}
?>