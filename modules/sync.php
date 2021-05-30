<?php
class WeMoSync {
    private static function DoWeMoObserve(){
        switch(Settings::LoadSettingsVar('do_wemo_observe','auto')){
            case "yes":
                return true;
            case "no":
                return false;
        }
        return Servers::IsHub();
    }
    public static function Observe(){
        if(WeMoSync::DoWeMoObserve()){
            // do wemo observe python command
            echo "\nDo python observer?\n";
        } else {
            echo "\nDo pull from hub?\n";
            WeMoSync::PullLightsFromHub();
        }
    }
    public static function PullLightsFromHub(){
        if(Servers::IsHub()) return null;
        $hub = Servers::GetHub();
        if(is_null($hub)) return null;
        $lights = LoadJsonArray("http://".$hub['url']."/api/light");
        foreach($lights["lights"] as $light){
            WeMoLights::SaveWeMo($light);
            WeMoLogs::SaveLog($light);
        }
    }
    public static function CheckWeMoServer($host){
        $wemo = WeMoSync::CheckWeMo($host['ip']);
        if($wemo && isset($wemo['mac_address'],$wemo['name'])){
            $host['type'] = "WeMo";
            WeMoLights::SaveWeMo($wemo);
        }
        return $host;
    }
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