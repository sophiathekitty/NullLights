<?php
define("ARCHIVE_SERVICE","WeMoArchiver::DeepArchive");
Services::Start("WeMoArchiver::DeepArchive");
$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::DeepArchiveWeMoArchiveMonth($light['mac_address'],date("m",time()-WeeksToSeconds(2)));
}
Services::Complete("WeMoArchiver::DeepArchive");
?>