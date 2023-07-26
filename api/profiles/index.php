<?php
require_once("../../../../includes/main.php");
if(isset($_GET['room_id'])){
    $data['profiles'] = LightingProfile::RoomId($_GET['room_id']);
} else {
    $data['profiles'] = LightingProfile::AllProfiles();
}
OutputJson($data);
?>