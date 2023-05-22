<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$lights = []; //WeMoLights::RoomLights($_GET['room_id']);
$others = [];
$groups = RoomLightsGroup::RoomDevices($_GET['room_id']);
foreach($groups as $group){
    if($group['type'] == "light") $lights[] = $group;
    else $others[] = $group;
}
if(count($lights) == 0 && count($others) == 0) die();
function LightItem($light){ ?><a href="#" title="<?=$light['name'];?>" light_id="<?=$light['id']?>" class="light" type="<?=$light['type']?>" subtype="<?=$light['subtype']?>" state="<?=$light['state']?>" target_state="<?=$light['target_state']?>" lock_state="<?=$light['lock_state']?>" error="<?=$light['error']?>"></a><?php }
?>
<nav class="lighting" collection="lights"><?php foreach($lights as $light) LightItem($light);?></nav><nav class="other" collection="lights"><?php foreach($others as $light) LightItem($light);?></nav>