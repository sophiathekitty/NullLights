<?php

/**
 * govee api module
 */
class GoveeAPI {
    /**
     * get device list
     * @return array the list of devices
     */
    public static function DeviceList(){
        // limit api calls to less than 10 per min
        $apiCalls= (int)Settings::LoadSettingsVar("govee_api_calls",0);
        if($apiCalls > 9) return null;
        Settings::SaveSettingsVar("govee_api_calls",$apiCalls+1);
        // api info
        $apiKey = Settings::LoadSettingsVar("govee_api_key");
        $url = 'https://developer-api.govee.com/v1/devices';
        // Set the request headers
        $headers = [
            'Govee-API-Key: ' . $apiKey,
            'Content-Type: application/json',
        ];
        // Initialize curl
        $curl = curl_init();
        // Set curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // Execute the request
        $response = curl_exec($curl);
        $data = [];
        // Check for errors
        if ($response === false) {
            Debug::Log(curl_error($curl));
        } else {
            // Process the response
            $responseData = json_decode($response, true);
            $data = $responseData['data'];
        }
        // Close curl
        curl_close($curl);
        // return the list of govee devices in an array of associated arrays
        return $data;
    }
    /**
     * get device state
     * @param string $mac_address the mac address for the device
     * @param string $model the model for the device
     * @return int the state of the device (on == 1)
     */
    public static function DeviceState($mac_address,$model){
        // limit api calls to less than 10 per min
        $apiCalls= (int)Settings::LoadSettingsVar("govee_api_calls",0);
        Debug::Log("GoveeAPI::DeviceState::apiCalls",$apiCalls);
        if($apiCalls > 9) return null;
        Settings::SaveSettingsVar("govee_api_calls",$apiCalls+1);
        // api info
        $apiKey = Settings::LoadSettingsVar("govee_api_key");
        $url = "https://developer-api.govee.com/v1/devices/state?device=".urlencode($mac_address)."&model=".urlencode($model);
        // Set the request headers
        $headers = [
            'Govee-API-Key: ' . $apiKey
        ];
        // Initialize curl
        $curl = curl_init();
        // Set curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // Execute the request
        $response = curl_exec($curl);
        $state = -1;
        // Check for errors
        if ($response === false) {
            Debug::Log("GoveeAPI::DeviceState::curl_error",curl_error($curl));
        } else {
            // Process the response
            $responseData = json_decode($response, true);
            Debug::Log("GoveeAPI::DeviceState::responseData",$responseData);
            Debug::Log("GoveeAPI::DeviceState::powerState",$responseData['data']['properties'][1]['powerState']);
            $state = ($responseData['data']['properties'][1]['powerState'] === 'on') ? 1 : 0;
        }
        // Close curl
        curl_close($curl);
        Debug::Log("GoveeAPI::DeviceState::state",$state);
        return $state;
    }
    /**
     * set device state
     * @param string $mac_address the mac address for the device
     * @param string $model the model for the device
     * @param int $state the target state (1 = on, 0 = off)
     * @return bool true if success
     */
    public static function SetState($mac_address,$model,$state){
        // limit api calls to less than 10 per min
        $apiCalls= (int)Settings::LoadSettingsVar("govee_api_calls",0);
        Debug::Log("GoveeAPI::SetState:apiCalls",$apiCalls);
        if($apiCalls > 9) return null;
        Settings::SaveSettingsVar("govee_api_calls",$apiCalls+1);
        // api info
        $apiKey = Settings::LoadSettingsVar("govee_api_key");
        $state = ((int)$state == 1) ? "on" : "off";
        // API endpoint URL
        $url = "https://developer-api.govee.com/v1/devices/control";
        // Set the request headers
        $headers = [
            "Govee-API-Key: " . $apiKey,
            "Content-Type: application/json",
        ];
        // Set the request data
        $requestData = [
            "device" => $mac_address,
            "model" => $model,
            "cmd" => [
                "name" => "turn",
                "value" => $state,
            ],
        ];
        // Initialize curl
        $curl = curl_init();
        // Set curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        // Execute the request
        $response = curl_exec($curl);
        Debug::Log("GoveeAPI::SetState::response",$response);
        $success = false;
        // Check for errors
        if ($response === false) {
            Debug::Log(curl_error($curl));
        } else {
            // Process the response
            $responseData = json_decode($response, true);
            Debug::Log("GoveeAPI::SetState::responseData",$responseData);
            $success = true;
        }
        // Close curl
        curl_close($curl);
        return $success;
    }
    /**
     * set device brightness
     * @param string $mac_address the mac address for the device
     * @param string $model the model for the device
     * @param float $percent the target brightness (0.0 == 0% and 1.0 = 100%)
     * @return bool true if success
     */
    public static function SetBrightness($mac_address,$model,$percent){
        // limit api calls to less than 10 per min
        $apiCalls= (int)Settings::LoadSettingsVar("govee_api_calls",0);
        Debug::Log("GoveeAPI::DeviceState::apiCalls",$apiCalls);
        if($apiCalls > 9) return null;
        Settings::SaveSettingsVar("govee_api_calls",$apiCalls+1);
        // api info
        $apiKey = Settings::LoadSettingsVar("govee_api_key");
        $brightness = 100 * $percent;
        // API endpoint URL
        $url = "https://developer-api.govee.com/v1/devices/control";
        // Set the request headers
        $headers = [
            "Govee-API-Key: " . $apiKey,
            "Content-Type: application/json",
        ];
        // Set the request data
        $requestData = [
            "device" => $mac_address,
            "model" => $model,
            "cmd" => [
                "name" => "brightness",
                "value" => $brightness,
            ],
        ];
        // Initialize curl
        $curl = curl_init();
        // Set curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        // Execute the request
        $response = curl_exec($curl);
        $success = false;
        // Check for errors
        if ($response === false) {
            Debug::Log(curl_error($curl));
        } else {
            // Process the response
            $responseData = json_decode($response, true);
            Debug::Log("GoveeAPI::SetState::responseData",$responseData);
            $success = true;
        }
        // Close curl
        curl_close($curl);
        return $success;
    }
}
?>