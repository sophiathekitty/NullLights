<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$lights = WeMoLights::RoomLights($_GET['room_id']);
$others = WeMoLights::RoomWeMos($_GET['room_id'],"other");
if(count($lights) == 0 && count($others) == 0) die();
function LightItem($light){ ?><a href="#" title="<?=$light['name'];?>" mac_address="<?=$light['mac_address']?>" class="light" type="<?=$light['type']?>" subtype="<?=$light['subtype']?>" state="<?=$light['state']?>" target_state="<?=$light['target_state']?>" lock_state="<?=$light['lock_state']?>" error="<?=$light['error']?>"></a><?php }
?>
<nav class="lighting" collection="lights"><?php foreach($lights as $light) LightItem($light);?></nav><nav class="other" collection="lights"><?php foreach($others as $light) LightItem($light);?></nav>