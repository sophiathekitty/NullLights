<?php
WeMoSync::Observe();
if(is_file($root_path."plugins/NullLights/scripts/lights.php")){
    echo "\nlegacy light automation go\n";
    Settings::SaveSettingsVar("service-AutomationLegacy--start",date("H:i:s"));
    IncludeFolder($root_path."plugins/NullLights/scripts/");
    $lights = WeMoLights::AllLights();
    foreach($lights as $light){
        if(AutomateLight($light)){
            //Settings::SaveSettingsVar("service-AutomationLegacy::".$light['name'],date("H:i:s"));
        }
    }
    echo "\nlegacy light automation done\n";
    Settings::SaveSettingsVar("service-AutomationLegacy--done",date("H:i:s"));
} else {
    Settings::SaveSettingsVar("service-AutomationLegacy--missing",date("H:i:s"));
}
?>