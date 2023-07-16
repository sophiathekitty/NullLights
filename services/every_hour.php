<?php
Services::Start("NullLights::EveryHour");
Services::Log("NullLights::EveryHour","TuyaSync::Discover");
TuyaSync::Discover();
Services::Log("NullLights::EveryHour","GoveeSync::Discover");
GoveeSync::Discover();
Services::Complete("NullLights::EveryHour");
?>