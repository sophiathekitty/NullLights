class LightProfileIcon extends View {
    constructor(debug = true){
        super(
            new CurrentLightProfileModel(),
            null,
            null,60000,debug);
    }
    build(){
        if(this.debug) console.warn("LightProfileIcon::Build","missing room id");
    }
    display(){
        if(this.debug) console.warn("LightProfileIcon::Display","missing room id");
    }
    refresh(){
        if(this.debug) console.warn("LightProfileIcon::Refresh","missing room id");
    }
    build(room_id){
        if(this.debug) console.log("LightProfileIcon::Build",room_id);
        
    }
    display(room_id){
        if(this.debug) console.info("LightProfileIcon::Display",room_id);

        this.model.getRoomProfile(room_id,json=>{
            if(this.debug) console.info("LightProfileIcon::Display",room_id,"profile",json);
            if(json.profile != null){
                if(this.debug) console.log("LightProfileIcon::Display",room_id,json.profile);
                if($("[room_id="+room_id+"] .sensors .profiles").html() == ""){
                    $("<span class='light_profile'></span>").appendTo("[room_id="+room_id+"] .profiles");
                }
                $("[room_id="+room_id+"] .value[var=current_light_profile]").attr("val",json.profile.id);
                $("[room_id="+room_id+"] .value[var=current_light_profile]").html(json.profile.name);
                $("[room_id="+room_id+"] .edit[var=current_light_profile]").val(json.profile.id);
                $("[room_id="+room_id+"] .light_profile").attr("type",json.profile.type);
                $("[room_id="+room_id+"] .light_profile").css("background-image","url(/plugins/NullLights/img/types/"+json.profile.type+".png)");
                var tool_tip = "Lighting Profile: "+json.profile.name;
                if(json.profile.light_level_min != null) tool_tip += "\nlight min: "+json.profile.light_level_min;
                if(json.profile.light_level_max != null) tool_tip += "\nlight max: "+json.profile.light_level_max;
                json.profile.devices.forEach(device => {
                    tool_tip += "\n"+device.name+": "+device.mode_txt;
                });
                $("[room_id="+room_id+"] .light_profile").attr("title",tool_tip);
                $("[room_id="+room_id+"] .value[var=current_light_profile]").attr("title",tool_tip);

            } else {
                if(this.debug) console.log("LightProfileIcon::Display",room_id,json.profile);
                $("[room_id="+room_id+"] .profiles").html("");
            }
        });
    }
    refresh(room_id){
        if(this.debug) console.info("LightProfileIcon::Refresh",room_id);
        this.display(room_id);
    }
}