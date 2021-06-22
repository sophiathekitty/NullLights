<?php
$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::ArchiveWeMoLogsYesterday($light['mac_address']);
}
?>