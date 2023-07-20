<?php
define("ARCHIVE_SERVICE","RoomLightGroupArchiver::DeepArchive");
Services::Start("RoomLightGroupArchiver::DeepArchive");
/*
$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::DeepArchiveWeMoArchiveMonth($light['mac_address'],date("m",time()-WeeksToSeconds(2)));
}
*/
$lights = RoomLightsGroup::AllLights();
foreach($lights as $light){
    RoomLightGroupArchiver::DeepArchiveRoomLightGroupArchiveMonth($light['id'],date("m",time()-WeeksToSeconds(4)));
}
Services::Complete("RoomLightGroupArchiver::DeepArchive");
?>