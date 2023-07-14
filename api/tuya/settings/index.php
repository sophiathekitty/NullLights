<?php
require_once("../../../../../includes/main.php");
$data = Settings::LoadSettingsPallet("tuya");
OutputJson($data);
?>
