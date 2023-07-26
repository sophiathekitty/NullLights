<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$profile = LightProfile::CurrentRoomProfile($_GET['room_id']);
$room_profiles = LightingProfile::RoomId($_GET['room_id']);
require_once("../../../includes/main.php");
?>
<ul class="light-profiles">
    <li>
        <span class="key">Current</span>
        <span class="value" var="current_light_profile" val="<?=$profile['id'];?>"><?=$profile['name'];?></span>
        <select class="edit" var="current_light_profile">
            <option value="0">None</option>
            <?php foreach($room_profiles as $room_profile) { ?><option value="<?=$room_profile['id'];?>"><?=$room_profile['name']?></option><?php } ?>
        </select>
    </li>
    <li>
        <a href="#create" action="create" room_id="<?=$_GET['room_id'];?>">New Lighting Profile</a>
    </li>
    <li>
        <a href="#edit" action="edit" room_id="<?=$_GET['room_id'];?>">Edit Lighting Profiles</a>
    </li>
</ul>