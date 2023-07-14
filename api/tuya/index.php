<?php
require_once("../../../../includes/main.php");
$data = [];
if(isset($_GET['id'])){
    $data['tuya'] = TuyaLights::DeviceID(($_GET['id']));
} else if(isset($_GET['light_id'])){
    $data['tuyas'] = TuyaLights::LightGroup(($_GET['light_id']));
} else {
    $data['tuyas'] = TuyaLights::AllLights();
}
OutputJson($data);
?>