<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    $data['govee'] = GoveeLights::MacAddress(($_GET['mac_address']));
} else {
    $data['govees'] = GoveeLights::AllLights();
}
OutputJson($data);
?>