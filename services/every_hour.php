<?php
Services::Start("NullLights::EveryHour");
Services::Log("NullLights::EveryHour","TuyaSync::Discover");
TuyaSync::Discover();
Services::Complete("NullLights::EveryHour");
?>