<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    $data['chart'] = [
        'light'=> WeMoLights::MacAddress($_GET['mac_address']),
        'hourly'=> WeMoChart::HourlyWeMoLog($_GET['mac_address'])];
} else if(isset($_GET['room_id'])) {
    $data['charts'] = RoomLightChart::HourlyRoomLightRoomLog($_GET['room_id']);
} else {
    $rooms = Rooms::AllRooms();
    $data['rooms'] = [];
    foreach($rooms as $room){
        $data['rooms'][] = [
            'room_id' => $room['id'],
            'charts' => RoomLightChart::HourlyRoomLightRoomLog($room['id'])
        ];
    }
}
OutputJson($data);
?>