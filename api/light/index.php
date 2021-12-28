<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    $data['light'] = WeMoLights::MacAddress(($_GET['mac_address']));
} else {
    $data['lights'] = WeMoLights::AllLights();
}
OutputJson($data);
?>