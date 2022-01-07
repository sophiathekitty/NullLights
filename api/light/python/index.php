<?php
require_once("../../../../../includes/main.php");
$data = [];
if(isset($_GET['mac_address'],$_GET['state'],$_GET['target_state'])){
    $data = WeMoLights::SaveWeMo(['mac_address'=>$_GET['mac_address'],'state'=>$_GET['state'],'target_state'=>$_GET['target_state']]);
} else if(isset($_GET['mac_address'],$_GET['find_port'])){
    $wemo = WeMoSync::FindWeMoPort($_GET['mac_address']);
    if(is_null($wemo)){
        $data['error'] = "wemo missing";
    }
    $data['light'] = WeMoLights::MacAddress($_GET['mac_address']);
} else if(isset($_GET['mac_address'])){
    $data['light'] = WeMoLights::MacAddress($_GET['mac_address']);

} else {
    $data['lights'] = WeMoLights::AllLights();
}
OutputJson($data);
?>
