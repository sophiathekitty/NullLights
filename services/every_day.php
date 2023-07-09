<?php
define("ARCHIVE_SERVICE","WeMoArchiver::Archive");
Services::Start("WeMoArchiver::Archive");
/*$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::ArchiveWeMoLogsYesterday($light['mac_address']);
}*/
$lights = RoomLightsGroup::AllLights();
foreach($lights as $light){

}
Services::Complete("WeMoArchiver::Archive");
?>