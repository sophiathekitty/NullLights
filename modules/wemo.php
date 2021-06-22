<?php
class WeMo {
    /**
     * runs the python script to set the state of the actual wemo
     * @param array $wemo the wemo data object. needs $wemo['mac_address']
     */
    public static function SetState($wemo){
        echo shell_exec("python /var/www/html/plugins/NullLights/python/main.py ".$wemo['mac_address']);
    }
    /**
     * observes the current states for all of the known wemos
     */
    public static function Observe(){
        shell_exec("python /var/www/html/plugins/NullLights/python/main.py observe");
    }
    /**
     * log current wemo states
     */
    public static function Log(){
        $lights = WeMoLights::AllLights();
        foreach($lights as $light){
            WeMoLogs::AddLog($light['mac_address'],$light['state']);
        }
    }
}
?>