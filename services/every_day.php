<?php
define("ARCHIVE_SERVICE","RoomLightGroupArchiver::Archive");
Services::Start("RoomLightGroupArchiver::Archive");

$lights = RoomLightsGroup::AllLights();
foreach($lights as $light){
    try{
        RoomLightGroupArchiver::ArchiveRoomLightGroupLogsYesterday($light['id']);
    } catch(Error $e){
    Services::Error("RoomLightGroupArchiver::Archive",$e->getMessage());
    }
}
Services::Complete("RoomLightGroupArchiver::Archive");
?>