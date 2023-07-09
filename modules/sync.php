<?php
class WeMoSync {
    /**
     * should we observe the wemos directly? (run python script)
     * @return bool returns true if do_we_observe is set to "yes" or if this is the hub server
     */
    private static function DoWeMoObserve(){
        Debug::Trace("WeMoSync::DoWeMoObserve");
        switch(Settings::LoadSettingsVar('do_wemo_observe','auto')){
            case "yes":
                return true;
            case "no":
                return false;
        }
        return Servers::IsHub();
    }
    /**
     * observe the wemo states
     */
    public static function Observe(){
        Debug::Trace("WeMoSync::Observe");
        Services::Start("NullLights::WeMoSync::Observe");
        if(WeMoSync::DoWeMoObserve()){
            //Settings::SaveSettingsVar("service::ObserveLights","observer ".date("H:i:s"));
            Services::Log("NullLights::WeMoSync::Observe","WeMo::Observe -- start");
            // do wemo observe python command
            WeMo::Observe();
            Services::Log("NullLights::WeMoSync::Observe","WeMo::Observe -- done");
            WeMo::Log();
        } else {
            Debug::Log("Do pull from hub?");
            WeMoSync::PullLightsFromHub();
        }
        // cache room_light_on
        $rooms = Rooms::AllRooms();
        foreach($rooms as $room){
            $lights = WeMoLights::RoomLights($room['id']);
            $on = 0;
            foreach($lights as $light){
                if((int)$light['state'] == 1 && $light['type'] == "light") $on = 1;
            }
            Debug::Log("room: ".$room['name']." $on");
            Rooms::SaveRoom(['id'=>$room['id'],"lights_on_in_room"=>$on]);
        }
        Services::Complete("NullLights::WeMoSync::Observe");
    }
    /**
     * pulls the lights from the hub
     */
    public static function PullLightsFromHub(){
        Debug::Trace("WeMoSync::PullLightsFromHub");
        if(Servers::IsHub()) return null;
        $hub = Servers::GetHub();
        if(is_null($hub)) return null;
        //$lights = LoadJsonArray("http://".$hub['url']."/api/light");
        $url = "plugins/NullLights/api/wemos";
        $key = "wemos";
        if($hub['type'] == "old_hub"){
            $url = "/api/light";
            $key = "lights";
        } 
        $lights = ServerRequests::LoadHubJSON($url);
        Debug::Log("WeMoSync::PullLightsFromHub--remote lights",$lights);
        Services::Log("NullLights::WeMoSync::Observe","WeMoSync::PullLightsFromHub -- ".count($lights[$key]));
        //Settings::SaveSettingsVar("service::PullLights",count($lights)."|".date("H:i:s"));
        foreach($lights[$key] as $light){
            $l = WeMoLights::MacAddress($light['mac_address']);
            if($l['state'] != $light['state']) ManualLogs::SaveLog($light);
            $save = WeMoLights::SaveWeMo($light,true);
            Debug::Log("WeMoSync::PullLightsFromHub--save",$save);
            Services::Log("NullLights::WeMoSync::Observe",$light['name']." ".$light['state']);
            //WeMoLogs::SaveLog($light);
        }
        Debug::Log("WeMoSync::PullLightsFromHub--local lights",WeMoLights::AllLights());
    }
    /**
     * checks if a host is a wemo and saves it to the wemo table if it is
     * @param array $host data object with $host['ip'] set
     * @return array returns the $host object with the $host['type'] set to "WeMo" if host is a wemo
     */
    public static function CheckWeMoServer($host){
        $wemo = WeMoSync::CheckWeMo($host['ip']);
        if($wemo && isset($wemo['mac_address'],$wemo['name'])){
            $host['type'] = "WeMo";
            WeMoLights::SaveWeMo($wemo);
        }
        return $host;
    }
    /**
     * checks a wemo at an ip address
     * @param string $ip the ip address to check
     * @return array returns a wemo data array
     */
    public static function FindWeMoPort($mac_address){
        $wemo = WeMoLights::MacAddress($mac_address);
        $wemo = WeMoSync::CheckWeMo($wemo['url']);
        if($wemo && isset($wemo['mac_address'],$wemo['name'])){
            WeMoLights::SaveWeMo($wemo);
        }
        return $wemo;
    }
    /**
     * checks a wemo at an ip address
     * @param string $ip the ip address to check
     * @return array returns a wemo data array
     */
    private static function CheckWeMo($ip){
        $wemo = ['url'=>$ip,'port'=>49153];
        $ports = WeMoSync::WeMoPorts($ip);
        if(count($ports) == 0) return null;
        foreach($ports as $port){
            $content=@file_get_contents("http://".$ip.":".$port."/setup.xml");
            if(!is_null($content) && $content != ""){
                // found
                $wemo['port'] = $port;
                break;
            }
        }
        
        if(is_null($content) || $content == "")
            return null;
        //echo "|CONTENT FOUND|";
        preg_match('/<friendlyName>(.*)?<\/friendlyName>/', $content, $match);
        if(count($match) > 0) $wemo['name'] = $match[1];
        preg_match('/<macAddress>(.*)?<\/macAddress>/', $content, $match);
        if(count($match) > 0) $wemo['mac_address'] = $match[1];
        return $wemo;
    }
    /**
     * finds the valid wemo ports to check
     * @param string $ip the ip address to scan
     * @return array returns list of ports to try
     */
    private static function WeMoPorts($ip){
        $raw_output = shell_exec("nmap $ip");
        $lines = explode("\n",$raw_output);
        $ports = [];
        foreach($lines as $line){
            if(strpos($line,'/tcp') > 0){
                //echo "|$line|\n\n";
                list($port) = explode("/tcp",$line);
                //echo "|$port|\n\n";
                array_push($ports,$port);
            }
        }
        //echo $ports;
        return $ports;
    }


}
?>