<?php
require_once("../../../../../includes/main.php");
$data = [];
if(isset($_GET['floor'])){
    $data = FloorLights($_GET['floor']);
} else {
    $data['floors'] = [];
    $floor = Rooms::Floor('second');
    if(count($floor['rooms'])) $data['floors'][] = FloorLights($floor['floor']);
    $floor = Rooms::Floor("ground");
    if(count($floor['rooms'])) $data['floors'][] = FloorLights($floor['floor']);
    $floor = Rooms::Floor("basement");
    if(count($floor['rooms'])) $data['floors'][] = FloorLights($floor['floor']);
}

function FloorLights($floor){
    $rooms = Rooms::Floor($floor);
    $lights = [];
    foreach($rooms['rooms'] as $room){
        $room_lights = WeMoLights::RoomLights($room['id']);
        //$room_lights = RoomLightsGroup::RoomLightsGroupLight($room['id']);
        $lights = array_merge($lights,$room_lights);
    }
    return ['floor'=>$floor,'lights'=>$lights];
}

OutputJson($data);
?>