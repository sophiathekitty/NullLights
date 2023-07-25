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
    try{
        RoomLightGroupArchiver::DeepArchiveRoomLightGroupArchiveMonth($light['id'],date("m",time()-WeeksToSeconds(4)));
    } catch(Error $e) {
        Services::Error("NullLights::EveryMinute",$e->getMessage());
    }
}
Services::Complete("RoomLightGroupArchiver::DeepArchive");
?>