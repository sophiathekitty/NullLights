<?php
Services::Start("NullLights::EveryHour");
define("DEVICE_GROUP_SERVICE","NullLights::EveryHour");
define("DEVICE_DISCOVER_SERVICE","NullLights::EveryHour");
Services::Log("NullLights::EveryHour","TuyaSync::Discover");
try{
    TuyaSync::Discover();
} catch(Exception $e){
    Services::Error("NullLights::EveryHour",$e->getMessage());
}
Services::Log("NullLights::EveryHour","GoveeSync::Discover");
try{
    GoveeSync::Discover();
} catch(Exception $e){
    Services::Error("NullLights::EveryHour",$e->getMessage());
}
Services::Complete("NullLights::EveryHour");
?>