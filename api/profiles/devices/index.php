<?php
require_once("../../../../../includes/main.php");
if(isset($_GET['profile_id'])){
    $data['devices'] = LightProfileDevice::LightProfileId($_GET['profile_id']);
} else {
    $data['devices'] = LightProfileDevice::AllLights();
}
OutputJson($data);
?>