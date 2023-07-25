<?php
Services::Start("NullLights::EveryTenMinute");
define("DEVICE_GROUP_SERVICE","NullLights::EveryTenMinute");
// make sure all the devices are in the right group
Services::Log("NullLights::EveryTenMinute","LightGroups::FindLightGroups");
try{
    LightGroups::FindLightGroups();
} catch(Error $e){
    Services::Error("NullLights::EveryTenMinute",$e->getMessage());
}
// prune empty groups
Services::Log("NullLights::EveryTenMinute","LightGroups::PruneEmptyGroups");
try{
    LightGroups::PruneEmptyGroups();
} catch(Error $e){
    Services::Error("NullLights::EveryTenMinute",$e->getMessage());
}

Services::Complete("NullLights::EveryTenMinute");
?>