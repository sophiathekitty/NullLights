<?php
$lights = WeMoLights::AllLights();
foreach($lights as $light){
    WeMoArchiver::DeepArchiveWeMoArchiveMonth($light['mac_address'],date("m",time()-WeeksToSeconds(2)));
}
?>