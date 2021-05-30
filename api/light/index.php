<?php
require_once("../../../../includes/main.php");
$data = [];
$data['lights'] = WeMoLights::AllLights();
OutputJson($data);
?>