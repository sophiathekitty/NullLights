<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$profile = LightProfile::CurrentRoomProfile($_GET['room_id']);
$tool_tip = "Lighting Profile: ".$profile['name'];
if(!is_null($profile['light_level_min'])) $tool_tip .= "
light min: ".$profile['light_level_min'];
if(!is_null($profile['light_level_max'])) $tool_tip .= "
light max: ".$profile['light_level_max'];
foreach($profile['devices'] as $device){
    $tool_tip .= "
".$device['name'].": ".$device['mode_txt'];
}
?><span class="light_profiles"><?php 
if(!is_null($profile)){ ?><span class="light_profile" type="<?=$profile['type']?>" style="background-image: url(/plugins/NullLights/img/types/<?=$profile['type'];?>.png);" title="<?=$tool_tip;?>"></span><?php }
?></span>