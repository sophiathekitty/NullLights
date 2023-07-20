class RoomLightsView extends View {
    constructor(debug = LightsCollection.debug_lights){
        if(debug) console.info("RoomLightsView::Constructor");
        super(new LightsCollection(),new Template("light_groups","/plugins/NullLights/templates/groups.html"),new Template("light_button","/plugins/NullLights/templates/light.html"),60000,debug);
        this.charts = new LightsPixelChart();
    }
    build(){
        if(this.debug) console.warn("RoomLightsView::Build missing room_id");
    }
    display(){
        if(this.debug) console.warn("RoomLightsView::Display missing room_id");
    }
    refresh(){
        if(this.debug) console.warn("RoomLightsView::Refresh missing room_id");
    }
    refreshStates(){
        if(this.model){
            this.model.getData(json=>{
                json.lights.forEach(light=>{
                    $(".light[light_id="+light.id+"]").attr("type",light.type);
                    $(".light[light_id="+light.id+"]").attr("subtype",light.subtype);
                    $(".light[light_id="+light.id+"]").attr("state",light.state);
                    $(".light[light_id="+light.id+"]").attr("target_state",light.target_state);
                    $(".light[light_id="+light.id+"]").attr("lock_state",light.lock_state);
                    $(".light[light_id="+light.id+"]").attr("error",light.error);
                });
            });
        }
    }
    build(room_id){
        if(this.debug) console.info("RoomLightsView::Build",room_id,$("[room_id="+room_id+"]"));
        if($("[room_id="+room_id+"]").length){
            if(this.model && this.template && this.item_template){
                this.template.getData(html=>{
                    $(html).appendTo("[room_id="+room_id+"] .lights");
                    this.item_template.getData(itm_html=>{
                        if(this.debug) console.log("RoomLightsView::Build",room_id,"template",itm_html);
                        this.model.getRoomLights(room_id,json=>{
                            if(this.debug) console.log("RoomLightsView::Build",room_id,"lights",json);
                            json.forEach(light => {
                                var lighting = "other";
                                if(light.type == "light")  lighting = "lighting"
                                $(itm_html).appendTo("[room_id="+room_id+"] ."+lighting).attr("light_id",light.id);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("type",light.type);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("subtype",light.subtype);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("state",light.state);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("target_state",light.target_state);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("lock_state",light.lock_state);
                                $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("error",light.error);
                            });
                        });
                    });
                });
            }
            if(this.charts) this.charts.build(room_id);
        } else if(this.debug) console.error("RoomLightsView::Build",room_id,"Room Element Missing");
    }
    display(room_id){
        if(this.debug) console.info("RoomLightsView::Display",room_id);
        if($("[room_id="+room_id+"]").length){
            this.module.getRoomLights(room_id,json=>{
                json.forEach(light => {
                    $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("type",light.type);
                    $("[room_id="+room_id+"] [light_id="+light.id+"]").attr("subtype",light.subtype);
                    $("[collection=lights] [light_id="+light.id+"]").attr("state",light.state);
                    $("[collection=lights] [light_id="+light.id+"]").attr("target_state",light.target_state);
                    $("[collection=lights] [light_id="+light.id+"]").attr("lock_state",light.lock_state);
                    $("[collection=lights] [light_id="+light.id+"]").attr("error",light.error);
                });
            });
        } else if(this.debug) console.error("RoomLightsView::Display",room_id,"Room Element Missing");
    }
    refresh(room_id){
        if(this.charts) this.charts.refresh(room_id);
        if(this.debug) console.info("RoomLightsView::Refresh",room_id);
        if($("#floors [room_id="+room_id+"]").length){
            this.display(room_id);
            if(this.charts) this.charts.display(room_id);
        } else if(this.debug) console.error("RoomLightsView::Refresh",room_id,"Room Element Missing");
    }

}