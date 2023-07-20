<?php
define("ARCHIVE_SERVICE","RoomLightGroupArchiver::Archive");
Services::Start("RoomLightGroupArchiver::Archive");
/*$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::ArchiveWeMoLogsYesterday($light['mac_address']);
}*/

$lights = RoomLightsGroup::AllLights();
foreach($lights as $light){
    RoomLightGroupArchiver::ArchiveRoomLightGroupLogsYesterday($light['id']);
}
Services::Complete("RoomLightGroupArchiver::Archive");
?>