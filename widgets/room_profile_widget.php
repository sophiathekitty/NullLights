<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$profile = LightProfile::CurrentRoomProfile($_GET['room_id']);
$room_profiles = LightingProfile::RoomId($_GET['room_id']);
require_once("../../../includes/main.php");
?>
<ul class="current-light-profile">
    <li>
        <span class="key">Current</span>
        <span class="value" var="current_light_profile" val="<?=$profile['id'];?>"><?=$profile['name'];?></span>
        <select class="edit" var="current_light_profile">
            <option value="0">None</option>
            <?php foreach($room_profiles as $room_profile) { ?><option value="<?=$room_profile['id'];?>"><?=$room_profile['name']?></option><?php } ?>
        </select>
    </li>
    <?php foreach($profile['devices'] as $device) { ?><li class="profile_device">
        <span class="key"><?=$device['name']?></span>
        <span class="value"><?=$device['mode_txt'];?></span>
    </li><?php } ?>
</ul>
<ul class="light-profiles">
    <?php foreach($room_profiles as $profile){ ?><li class="light_profile_item">
        <a href="#" action="edit" profile_id="<?=$profile['id']?>" room_id="<?=$_GET['room_id'];?>"><?=$profile['name']?></a>
    </li><?php } ?>
    <li class="light_profile_nav">
        <a href="#" action="create" room_id="<?=$_GET['room_id'];?>">Create New</a>
    </li>
</ul>