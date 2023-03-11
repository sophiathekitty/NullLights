<?php
/**
 * uses request code from: https://github.com/3thirty/wemo-php/blob/3dbb739a472d840ec76c4118bea288f0964d77a2/Wemo.class.php
 */
class WeMo {
    const MAX_RETRIES = 3;   // try for a short time (if all fails will try again every minute until success... in theory...)
    const RETRY_SLEEP = 2;
    const REQUEST_TIMEOUT = 10;  // how long to wait for a response from the
                                        // device (in seconds) before trying again
    const DEVICE_FAULT = 98;     // we got a fault response from the device. This
                                        // seems like a harder error
    const DEVICE_ERROR = 99;     // we got an error response from the device. This
                                        // looks to happen when a device is already in
                                        // the requested state
    /**
     * apply the target state to the actual wemo
     * @param array $wemo the wemo data object. needs $wemo['mac_address'] and $wemo['target_state'] and $wemo['state']
     * @return array save report ['last_insert_id'=>$id,'error'=>clsDB::$db_g->get_err(),'sql'=>$sql,'row'=>$row]
     */
    public static function SetState($wemo){
        //shell_exec("python /var/www/html/plugins/NullLights/python/main.py ".$wemo['mac_address']);
        $current_state = WeMo::GetBinaryState($wemo);
        
        if(is_null($current_state)){
            $wemo['error'] = 1;
        } else {
            $wemo['error'] = 0;
            $wemo['state'] = $current_state;
        }
        if($wemo['state'] == $wemo['target_state']){
            $wemo['target_state'] = -1;
        } else if(WeMo::SetBinaryState($wemo,$wemo['target_state'])){
            $wemo['state'] = $wemo['target_state'];
            $wemo['target_state'] = -1;
            ManualLogs::SaveLog($wemo);
        }
        
        return WeMoLights::SaveWeMo($wemo);
    }
    /**
     * observes the current states for all of the known wemos
     */
    public static function Observe(){
        //shell_exec("python /var/www/html/plugins/NullLights/python/main.py observe");
        $wemos = WeMoLights::AllLights();
        foreach($wemos as $wemo){
            $current_state = WeMo::GetBinaryState($wemo);
            if(is_null($current_state)){
                $wemo['error'] = 1;
            } else {
                $wemo['error'] = 0;
                if($wemo['state'] != $current_state){
                    // wemo has changed state
                    ManualLogs::SaveLog($wemo);
                }
                $wemo['state'] = $current_state;
            }
            if($wemo['state'] == $wemo['target_state']){
                $wemo['target_state'] == -1;
            }
            WeMoLights::SaveWeMo($wemo);
        }
    }
    /**
     * log current wemo states
     */
    public static function Log(){
        WeMo::Observe();
        $lights = WeMoLights::AllLights();
        foreach($lights as $light){
            WeMoLogs::AddLog($light['mac_address'],$light['state']);
        }
    }
    /**
     * this attempts to set the binary state of the wemo device
     * @param array $wemo the wemo data object
     * @param int $target_state the binary state 1 for on 0 for off
     * @return bool true if success false if failed
     */
    
    public static function SetBinaryState($wemo,$target_state){
        $res = WeMo::sendRequest($wemo,"SetBinaryState","<BinaryState>" . $target_state . "</BinaryState>");
        $parsedResponse = self::parseResponse($res, "BinaryState");
        $ret = ($parsedResponse == $target_state);
        return $ret;
    }
    
    /**
     * this attempts to get the current binary state of the wemo device
     * @param array $wemo the wemo data object
     * @return int|null the binary state 1 for on 0 for off and null for error
     */
    
    public static function GetBinaryState($wemo){
        $res = WeMo::sendRequest($wemo,"GetBinaryState");
        if($res === false) return null;
        $parsedResponse = self::parseResponse($res, "BinaryState");
        return (int)$parsedResponse;
    }
    
    /**
     * sends a soap request to the wemo... untested...
     * @param array $wemo the wemo data object. needs $wemo['url'] and $wemo['port']...
     * @param string $action the action to be performed... ie: "SetBinaryState"
     * @param string $body the xml body to be sent for the action ie: "<BinaryState>0</BinaryState>" or "<BinaryState>1</BinaryState>"
     */
    
    private static function sendRequest($wemo,$action,$body = null, $attempt = 1){
        if($body === null){            
            if (strtoupper(substr($action, 0, 3)) == "GET"){
                $tag = substr($action,3);
            } else {
                $tag = $action;
                $action = "Get" . $action;
            }
            $body = $body = "<" . $tag . ">0</" . $tag. ">";
        }
        $header = "SOAPACTION: \"urn:Belkin:service:basicevent:1#" . $action . "\"";
        $data = '<?xml version="1.0" encoding="utf-8"?>'
            . '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">'
            . '<s:Body><u:' . $action . ' xmlns:u="urn:Belkin:service:basicevent:1">' . $body . '</u:' . $action . '></s:Body></s:Envelope>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_USERAGENT, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, self::REQUEST_TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, "http://" . $wemo['url'] . ":" . $wemo['port'] . "/upnp/control/basicevent1");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array (
            "Accept: ",
            "Content-type: text/xml; charset=\"utf-8\"",
            $header
        ));
        $ret = curl_exec($ch);
        /*
        if ($ret === false && $attempt++ < self::MAX_RETRIES){
            sleep(self::RETRY_SLEEP);
            return WeMo::sendRequest($wemo,$action, $body,$attempt);
        }
        */
        return $ret;
    }
    
    /**
     * parse the xml response to find the tagged value
     * @param string $xml the raw xml response
     * @param string $tag the xml tag to parse out
     * @return bool|int|string the tagged value or some error stuff
     */
    
    private static function parseResponse($xml,$tag){
        if (strpos($xml, "<faultstring>") !== FALSE)
            return self::DEVICE_FAULT;
        preg_match("/<" . $tag . ">(.*)<\/" . $tag . ">/", $xml, $matches);
        $ret = false;
        if ($matches[1])
            $ret = (string)$matches[1];
        if ($ret == "Error")
            $ret = self::DEVICE_ERROR;
        return $ret;
    }
    
}
?>