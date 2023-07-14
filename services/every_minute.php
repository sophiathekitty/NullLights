<?php
Services::Start("NullLights::EveryMinute");
Services::Log("NullLights::EveryMinute","WeMoSync::Observe");
WeMoSync::Observe();
Services::Log("NullLights::EveryMinute","TuyaSync::Observe");
TuyaSync::Observe();
Services::Log("NullLights::EveryMinute","LightGroups::SyncStatesFromMembers");
LightGroups::SyncStatesFromMembers();
if(Servers::IsMain() || Settings::LoadSettingsVar("light_automation_mode","main") == "practice"){
    Services::Log("NullLights::EveryMinute","Do automation ".Settings::LoadSettingsVar("light_automation_mode","main"));
    if(is_file($root_path."plugins/NullLights/scripts/lights.php")){
        //echo "\nlegacy light automation go\n";
        Services::Log("NullLights::EveryMinute","AutomationLegacy--start");
        Services::Start("NullLights::AutomationLegacy");
        //Settings::SaveSettingsVar("service-AutomationLegacy--start",date("H:i:s"));
        IncludeFolder($root_path."plugins/NullLights/scripts/");
        $lights = WeMoLights::AllLights();
        foreach($lights as $light){
            if(AutomateLight($light)){
                //Services::Log("NullLights::AutomationLegacy","Automated: ".$light['name']." ".$light['state']);
                //Settings::SaveSettingsVar("service-AutomationLegacy::".$light['name'],date("H:i:s"));
            }
        }
        //echo "\nlegacy light automation done\n";
        //Settings::SaveSettingsVar("service-AutomationLegacy--done",date("H:i:s"));
        Services::Complete("NullLights::AutomationLegacy");
        Services::Log("NullLights::EveryMinute","AutomationLegacy--done");
    } else {
        Services::Log("NullLights::EveryMinute","AutomationLegacy--missing");
        //Settings::SaveSettingsVar("service-AutomationLegacy--missing",date("H:i:s"));
    }    
} else {
    Services::Log("NullLights::EveryMinute","Skip automation ".Settings::LoadSettingsVar("light_automation_mode","main"));
}
Services::Complete("NullLights::EveryMinute");
?>