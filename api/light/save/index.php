<?php
require_once("../../../../../includes/main.php");
$data = [];
if(isset($_GET['lights'])){
    $data['get_lights'] = $_GET['lights'];
    foreach($_GET['lights'] as $light){
        WeMoLights::SaveWeMo($light);
        WeMo::SetState($light);
    }
    WeMo::Observe();
    $data['lights'] = WeMoLights::AllLights();
} else if(isset($_GET['mac_address'],$_GET['target_state'])) {
    WeMoLights::SaveWeMo($_GET);
    WeMo::SetState($_GET);
    $data['light'] = WeMoLights::MacAddress($_GET['mac_address']);
} else if(isset($_GET['light_id'],$_GET['target_state'])) {
    LightGroups::SetState($_GET['light_id'],$_GET['target_state']);
    $data['light'] = RoomLightsGroup::LightId($_GET['light_id']);
}
OutputJson($data);
?>
