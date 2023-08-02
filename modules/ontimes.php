<?php
/**
 * get the room's lights with some extra info added
 * @param int $room_id the room's id
 * @return array array of lights with extra info
 */
function RoomLightsStamps($room_id){
    $lights = WeMoLights::RoomLights($room_id);
    return ParseLightStampStats($lights);
}
/**
 * get all the lights with some extra info added
 * @return array array of all the lights with some extra info added
 */
function AllLightsStamps(){
    $lights = WeMoLights::AllLights();
    return ParseLightStampStats($lights);
}
/**
 * adds isLight, mode, on_time, and error_percent to lights in array
 * @param array $lights an array of lights
 * @return array the $lights array but now the lights have extra info added
 */
function ParseLightStampStats($lights){
    for($i = 0; $i < count($lights); $i++){
        $lights[$i]['isLight'] = IsALight($lights[$i]);
        $lights[$i]['mode'] = LoadSettingVar($lights[$i]['name']);
        //$lights[$i]['off_time'] = SecondsToTimeShort(WeMoOffTime($lights[$i]['mac_address'],(5*60)+61));
        $lights[$i]['on_time'] = SecondsToTimeShort(WeMoOnTime($lights[$i]['mac_address'],(7*60*60)+61));
        //$lights[$i]['off_now_time'] = SecondsToTimeShort(WeMoOffNowTime($lights[$i]['mac_address']));
        //$lights[$i]['on_now_time'] = SecondsToTimeShort(WeMoOnNowTime($lights[$i]['mac_address']));
        $lights[$i]['error_percent'] = WeMoErrorPercent($lights[$i]['mac_address'],0.01);
    }
    return $lights;
}
/**
 * returns true if light is a light
 * @param array $light the light data array needs $light['name']
 * @return bool returns false if not a light
 */
function IsALight($light){
    if(strpos(strtoupper($light['name']),'WINDOW FAN') > -1) return false;
    if(strpos(strtoupper($light['name']),'LAVA BUBBLES') > -1) return false;
    if(strpos(strtoupper($light['name']),'PINK STARS') > -1) return false;
    return true;
}
/**
 * see if the lights are on in the room
 * @note uses room's lights_on_in_room value if the room has been modified recently
 * @param int $room_id room's id
 * @return bool true if any of the lights are on in room
 */
function LightsOnInRoom($room_id){
    $room = Rooms::RoomId($room_id);
    if(isset($room['modified']) && !is_null($room['modified'])){
        if(time() - strtotime($room['modified']) < 60) return (bool)$room['lights_on_in_room'];
    }
    
    $lights = WeMoLights::RoomLights($room_id);
    foreach($lights as $light){
        if($light['state'] == 1 && IsALight($light))
            $room['lights_on_in_room'] = 1;
            Rooms::SaveRoom($room);
            return true;
    }
    $room['lights_on_in_room'] = 0;
    Rooms::SaveRoom($room);
    return false;
}
/**
 * see if the lights are on in the room's neighbors
 * @note uses room's lights_on_in_neighbors value if the room has been modified recently
 * @param int $room_id room's id
 * @return bool true if any of the lights are on in room's neighbors
 */

function LightsOnInNeighbors($room_id){
    $room = Rooms::RoomId($room_id);
    Services::Log("NullLights::AutomationLegacy","LightsOnInNeighbors: ".$room['name']);
    if(isset($room['modified']) && !is_null($room['modified'])){
        Services::Log("NullLights::AutomationLegacy","LightsOnInNeighbors: cached ".$room['lights_on_in_neighbors']);
        return (bool)$room['lights_on_in_neighbors'];
    }
    $neighbors = RoomNeighbors::Neighbors($room_id);
    foreach($neighbors as $neighbor){
        $neighbor_id = $neighbor['neighbor_id'];
        if($room_id == $neighbor_id) $neighbor_id = $neighbor['room_id'];
        if(LightsOnInRoom($neighbor_id)){
            $room['lights_on_in_neighbors'] = 1;
            Rooms::SaveRoom($room);
            Services::Log("NullLights::AutomationLegacy","LightsOnInNeighbors: live (yes) $room_id|$neighbor_id ".$room['lights_on_in_neighbors']);
            return true;
        }
    }
    $room['lights_on_in_neighbors'] = 0;
    Rooms::SaveRoom($room);
    Services::Log("NullLights::AutomationLegacy","LightsOnInNeighbors: live (no) ".$room['lights_on_in_neighbors']);
    return false;
}
/**
 * get the number of the room's neighbors that have at least one light on
 * @param int $room_id room's id
 * @return int the number of neighbors with lights on
 */
function LightsOnInNeighborsCount($room_id){
    $neighbors = RoomNeighbors::Neighbors($room_id);
    $count = 0;
    foreach($neighbors as $neighbor){
        $neighbor_id = $neighbor['neighbor_id'];
        if($room_id == $neighbor_id) $neighbor_id = $neighbor['room_id'];
        if(LightsOnInRoom($neighbor_id)){
            $count++;
        }
    }
    return $count;
}


?>