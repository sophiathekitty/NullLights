<?php
require_once("../../../../../includes/main.php");
if(isset($_GET['room_id'])){
    $data['profile'] = LightProfile::CurrentRoomProfile($_GET['room_id']);
} else {
    $data['profiles'] = ActiveLightProfiles::AllActiveLightProfiles();
}
OutputJson($data);
?>