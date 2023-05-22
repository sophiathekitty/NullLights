<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'])){
    $data['wemo'] = WeMoLights::MacAddress(($_GET['mac_address']));
} else {
    $data['wemos'] = WeMoLights::AllLights();
}
OutputJson($data);
?>