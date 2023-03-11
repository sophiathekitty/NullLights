<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['light_id'])){
    $data['light'] = RoomLightsGroup::LightId(($_GET['light_id']));
} elseif(isset($_GET['mac_address'])){
    $data['light'] = WeMoLights::MacAddress(($_GET['mac_address']));
} else {
    $data['lights'] = WeMoLights::AllLights();
    $data['groups'] = RoomLightsGroup::AllLights();
}
OutputJson($data);
?>