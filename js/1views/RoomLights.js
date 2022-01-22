class RoomLightsView extends View {
    constructor(debug = true){
        if(debug) console.log("RoomLights::Constructor");
        super(new LightsCollection(),new Template("light_groups","/plugins/NullLights/templates/groups.html"),new Template("light_button","/plugins/NullLights/templates/light.html"),60000,debug);
    }
    build(){
        if(this.debug) console.warn("RoomLights::Build missing room_id");
    }
    display(){
        if(this.debug) console.warn("RoomLights::Display missing room_id");
    }
    refresh(){
        if(this.debug) console.warn("RoomLights::Refresh missing room_id");
    }
    refreshStates(){
        if(this.model){
            this.model.getData(json=>{
                json.lights.forEach(light=>{
                    $("[mac_address="+light.mac_address+"]").attr("state",light.state);
                    $("[mac_address="+light.mac_address+"]").attr("target_state",light.target_state);
                });
            });
        }
    }
    build(room_id){
        if(this.debug) console.log("RoomLights::Build",room_id,$("[room_id="+room_id+"]"));
        if($("[room_id="+room_id+"]").length){
            if(this.model && this.template && this.item_template){
                this.template.getData(html=>{
                    $(html).appendTo("[room_id="+room_id+"] .lights");
                    this.item_template.getData(itm_html=>{
                        if(this.debug) console.log("RoomLights::Build",room_id,"template",itm_html);
                        this.model.getRoomLights(room_id,json=>{
                            if(this.debug) console.log("RoomLights::Build",room_id,"lights",json);
                            json.forEach(light => {
                                var lighting = "other";
                                if(light.type == "light")  lighting = "lighting"
                                $(itm_html).appendTo("[room_id="+room_id+"] ."+lighting).attr("mac_address",light.mac_address);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("type",light.type);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("subtype",light.subtype);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("state",light.state);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("target_state",light.target_state);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("lock_state",light.lock_state);
                                $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("error",light.error);
                            });
                        });
                    });
                });
            }
        } else if(this.debug) console.error("RoomLights::Build",room_id,"Room Element Missing");
    }
    display(room_id){
        if(this.debug) console.warn("RoomLights::Display",room_id);
        if($("[room_id="+room_id+"]").length){
            this.module.getRoomLights(room_id,json=>{
                json.forEach(light => {
                    $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("type",light.type);
                    $("[room_id="+room_id+"] [mac_address="+light.mac_address+"]").attr("subtype",light.subtype);
                    $("[collection=lights] [mac_address="+light.mac_address+"]").attr("state",light.state);
                    $("[collection=lights] [mac_address="+light.mac_address+"]").attr("target_state",light.target_state);
                    $("[collection=lights] [mac_address="+light.mac_address+"]").attr("lock_state",light.lock_state);
                    $("[collection=lights] [mac_address="+light.mac_address+"]").attr("error",light.error);
                });
            });
        } else if(this.debug) console.error("RoomLights::Display",room_id,"Room Element Missing");
    }
    refresh(room_id){
        if(this.debug) console.warn("RoomLights::Refresh",room_id);
        if($("#floors [room_id="+room_id+"]").length){
            this.display(room_id);
        } else if(this.debug) console.error("RoomLights::Refresh",room_id,"Room Element Missing");
    }

}