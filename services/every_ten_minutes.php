<?php
Services::Start("NullLights::EveryTenMinute");
Services::Log("NullLights::EveryTenMinute","FindWemoLightGroups");
FindWemoLightGroups();
Services::Complete("NullLights::EveryTenMinute");
?>