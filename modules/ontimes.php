<?php

function RoomLightsStamps($room_id){
    $lights = WeMoLights::RoomLights($room_id);
    return ParseLightStampStats($lights);
}
function AllLightsStamps(){
    $lights = WeMoLights::AllLights();
    return ParseLightStampStats($lights);
}
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
function IsALight($light){
    if(strpos(strtoupper($light['name']),'WINDOW FAN') > -1) return false;
    if(strpos(strtoupper($light['name']),'LAVA BUBBLES') > -1) return false;
    if(strpos(strtoupper($light['name']),'PINK STARS') > -1) return false;
    return true;
}
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
function LightsOnInNeighbors($room_id){
    $room = Rooms::RoomId($room_id);
    if(isset($room['modified']) && !is_null($room['modified'])){
        if(time() - strtotime($room['modified']) < 60) return (bool)$room['lights_on_in_neighbors'];
    }
    $neighbors = RoomNeighbors::Neighbors($room_id);
    foreach($neighbors as $neighbor){
        if(LightsOnInRoom($neighbor)){
            $room['lights_on_in_neighbors'] = 1;
            Rooms::SaveRoom($room);
            return true;
        }
    }
    $room['lights_on_in_neighbors'] = 0;
    Rooms::SaveRoom($room);
    return false;
}

function LightsOnInNeighborsCount($room_id){
    $neighbors = RoomNeighbors::Neighbors($room_id);
    $count = 0;
    foreach($neighbors as $neighbor){
        if(LightsOnInRoom($neighbor)){
            $count++;
        }
    }
    return $count;
}


?>