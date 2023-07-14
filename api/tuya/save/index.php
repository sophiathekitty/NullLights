<?php
require_once("../../../../../includes/main.php");
$data = [];
if(isset($_GET['id'])){
    $save = TuyaLights::SaveTuya($_GET);
    $data['error'] = $save['error'];
    $data['tuya'] = TuyaLights::DeviceID($_GET['id']);
} 
OutputJson($data);
?>
