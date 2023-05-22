<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['light_id'])){
    $data['light'] = RoomLightsGroup::LightId(($_GET['light_id']));
} else {
    //$data['wemos'] = WeMoLights::AllLights();
    $data['lights'] = RoomLightsGroup::AllLights();
}
OutputJson($data);
?>