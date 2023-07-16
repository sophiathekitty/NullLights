<?php
/**
 * Govee control model
 */
class Govee{
    /**
     * find govee devices
     */
    public static function FindDevices(){        
        $data = GoveeAPI::DeviceList();
        Debug::Log("GoveeAPI::DeviceList",$data);
        foreach($data['devices'] as $device){
            Debug::Log("GoveeAPI::DeviceList::device",$device);
            $govee = Govee::ParseGoveeDevice($device);
            GoveeLights::SaveGovee($govee);
            RoomLightsGroup::GroupGovee($govee);
            $roomLight = RoomLightsGroup::LightId($govee['light_id']);
            if(!is_null($roomLight)){
                RoomLightsGroup::FindRoomLightRoom($roomLight);
            }
        }
    }
    /**
     * convert govee device json to null govee model
     * @param array $device the govee device json
     * @return array the govee model array
     */
    private static function ParseGoveeDevice($device){
        return [
            'mac_address' => $device['device'],
            'model' => $device['model'],
            'name' => $device['deviceName']
        ];
    }
    /**
     * observe the states of the govee devices
     */
    public static function Observe(){
        Settings::SaveSettingsVar("govee_api_calls",0);
        $govees = GoveeLights::GoveeDevices();
        foreach($govees as $govee){
            Debug::Log("Govee::Observe::before",$govee);
            // check to see if we should try to set the state
            if((int)$govee['target_state'] != -1 && (int)Settings::LoadSettingsVar("govee_api_calls",0) < 5){
                Govee::SetState($govee);
            }
            Govee::GetState($govee);
            Debug::Log("Govee::Observe::observed",$govee);
        }
    }
    /**
     * get the state of a specific govee device
     * @param array $govee the govee device model array (sent as a reference not a copy)
     */
    public static function GetState(&$govee){
        $state = GoveeAPI::DeviceState($govee['mac_address'],$govee['model']);
        $govee['error'] = 0;
        if($state == -1) $govee['error'] = 1;
        else $govee['state'] = $state;
        GoveeLights::SaveGovee($govee);
    }
    /**
     * set the state of a specific govee device
     * @param array $govee the govee device model array (sent as a reference not a copy)
     */
    public static function SetState(&$govee){
        Debug::Log("Govee:SetState",$govee);
        if((int)$govee['target_state'] == -1) return;
        if((int)$govee['target_state'] == (int)$govee['state']){
            Debug::Log("Govee:SetState::target_state matches state",$govee['state']);
            $govee['target_state'] = -1;
        } else if(GoveeAPI::SetState($govee['mac_address'],$govee['model'],$govee['target_state'])){
            $govee['state'] = $govee['target_state'];
            $govee['target_state'] = -1;
            $govee['error'] = 0;
            Debug::Log("Govee:SetState::success",$govee);
        } else {
            Debug::Log("Govee:SetState::fail");
            if((int)Settings::LoadSettingsVar("govee_api_calls",0) < 9) $govee['error'] = 1;
        }
        GoveeLights::SaveGovee($govee);
    }
    /**
     * set the percent of a specific govee device
     * @param array $govee the govee device model array (sent as a reference not a copy)
     */
    public static function SetPercent(&$govee){
        if((int)$govee['target_percent'] == -1) return;
        if((int)$govee['target_percent'] == (int)$govee['state'] || 
                GoveeAPI::SetState($govee['mac_address'],$govee['model'],$govee['target_percent'])){
            $govee['percent'] = $govee['target_percent'];
            $govee['target_percent'] = -1;
            GoveeLights::SaveGovee($govee);
            return;
        }
    }
}
?>