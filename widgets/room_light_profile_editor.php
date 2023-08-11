<?php
if(!isset($_GET['room_id'])) die();
require_once("../../../includes/main.php");
$room = Rooms::RoomId($_GET['room_id']);
if(isset($_GET['profile_id'])){
    // load a profile
    $profile = LightingProfile::LightProfileId($_GET['profile_id']);
    $profile_devices = LightProfileDevice::LightProfileId($_GET['profile_id']);
} else {
    // create a default profile
    $profile = ['id'=>0,'name'=>"unnamed profile",'type'=>'lamp','min_light_level'=>1,'max_light_level'=>2];
    $profile_devices = [];
}
$profile_types = LightProfile::LightingProfileTypes();
$room_lights = RoomLightsGroup::RoomDevices($_GET['room_id']);
foreach($room_lights as &$light){
    $light['profile_device_id'] = 0;
    $light['profile_device_state'] = "nothing";
    foreach($profile_devices as $device){
        if($device['light_id'] != $light['id']) continue;
        if(is_null($device['state'])) $light['profile_device_state'] = "-1";
        else $light['profile_device_state'] = $device['state'];
        $light['profile_device_id'] = $device['id'];
    }
}
?>
<dialog class="popup" id="lighting_profile_editor" room_id="<?=$_GET['room_id']?>" lighting_profile_id="<?=$profile['id']?>">
    <header>
        <h1>Lighting Profile for <?=$room['name'];?></h1>
    </header>    
    <main class="form">
        <ul class="profile_settings">
            <li>
                <label for="light_profile_name" class="key">Name</label>
                <input type="text" id="light_profile_name" value="<?=$profile['name']?>">
            </li>
            <li>
                <label for="light_profile_type" class="key">Type</label>
                <select id="light_profile_type">
                    <?php foreach($profile_types as $type) {?><option value="<?=$type;?>"<?php if($type == $profile['type']) echo " selected"; ?>><?=$type;?></option><?php } ?>
                </select>
            </li>
            <li>
                <label for="min_light_level" class="key">Min Light Level</label>
                <input type="number" id="min_light_level" default="1.5" min="0" max="4" step="0.01" value="<?=$profile['light_level_min']?>">
            </li>
            <li>
                <label for="max_light_level" class="key">Max Light Level</label>
                <input type="number" id="max_light_level" default="2.5" min="0" max="4" step="0.01" value="<?=$profile['light_level_max']?>">
            </li>
        </ul>
        <ul class="profile_devices">
            <?php foreach($room_lights as &$light) { ?><li>
                <span class="key"><?=$light['name'];?></span>
                <select profile_device_id="<?=$light['profile_device_id'];?>" light_id="<?=$light['id'];?>">
                    <option value="nothing"<?php if($light['profile_device_state'] == "nothing") echo " selected"; ?>>Nothing</option>
                    <option value="-1"<?php if($light['profile_device_state'] == "-1") echo " selected"; ?>>Stay On</option>
                    <option value="1"<?php if($light['profile_device_state'] == "1") echo " selected"; ?>>Turn On</option>
                    <option value="0"<?php if($light['profile_device_state'] == "0") echo " selected"; ?>>Turn Off</option>
                </select>
            </li><?php } ?>
        </ul>
    </main>
    <div class="error_message"></div>
    <footer>
        <nav>
            <a action="save" href="#Save">Save</a>
            <a action="cancel" href="#cancel">Cancel</a>
            <a action="delete" href="#delete">Delete</a>
        </nav>
    </footer>
</dialog>