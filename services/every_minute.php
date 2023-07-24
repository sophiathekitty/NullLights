<?php
Services::Start("NullLights::EveryMinute");
Services::Log("NullLights::EveryMinute","WeMoSync::Observe");
try{
    WeMoSync::Observe();
} catch(Exception $e) {
    Services::Error("NullLights::EveryMinute",$e->getMessage());
}
Services::Log("NullLights::EveryMinute","TuyaSync::Observe");
try{
    TuyaSync::Observe();
} catch(Exception $e) {
    Services::Error("NullLights::EveryMinute",$e->getMessage());
}
Services::Log("NullLights::EveryMinute","GoveeSync::Observe");
try{
    GoveeSync::Observe();
} catch(Exception $e) {
    Services::Error("NullLights::EveryMinute",$e->getMessage());
}
Services::Log("NullLights::EveryMinute","LightGroups::SyncStatesFromMembers");
try{
    LightGroups::SyncStatesFromMembers();
} catch(Exception $e) {
    Services::Error("NullLights::EveryMinute",$e->getMessage());
}
if(Servers::IsMain() || Settings::LoadSettingsVar("light_automation_mode","main") == "practice"){
    Services::Log("NullLights::EveryMinute","Do automation ".Settings::LoadSettingsVar("light_automation_mode","main"));
    if(is_file($root_path."plugins/NullLights/scripts/lights.php")){
        //echo "\nlegacy light automation go\n";
        Services::Log("NullLights::EveryMinute","AutomationLegacy--start");
        Services::Start("NullLights::AutomationLegacy");
        //Settings::SaveSettingsVar("service-AutomationLegacy--start",date("H:i:s"));
        try{
            IncludeFolder($root_path."plugins/NullLights/scripts/");
            $lights = WeMoLights::AllLights();
            foreach($lights as $light){
                if(AutomateLight($light)){
                    Services::Log("NullLights::AutomationLegacy","Automated: ".$light['name']." ".$light['state']);
                    //Settings::SaveSettingsVar("service-AutomationLegacy::".$light['name'],date("H:i:s"));
                }
            }
        } catch(Exception $e) {
            Services::Error("NullLights::EveryMinute",$e->getMessage());
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