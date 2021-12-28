<?php
//echo "hello";
require_once("../../../includes/main.php");
//echo " world";
$settings = new Settings();

$data = PluginAPIs("NullLights/");
OutputJson($data);
?>