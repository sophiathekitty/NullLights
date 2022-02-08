class WeMoListView extends View {
    constructor(debug = LightsCollection.debug_lights){
        if(debug) console.log("WeMoListView::Constructor");
        super(LightsCollection.instance,null,new Template("wemo","/plugins/NullLights/templates/wemo.html"),60000,debug);
    }
    build(){
        if(this.debug) console.log("WeMoListView::Build");
        this.display();
    }
    display(){
        if(this.debug) console.log("WeMoListView::Display");
        if(this.model && this.item_template){
            this.item_template.getData(html=>{
                this.model.getData(json=>{
                    $("ul#wemos").html("");
                    if(this.debug) console.log("WeMoListView::Display",json);
                    json.lights.forEach((light,index)=>{
                        $(html).appendTo("ul#wemos").attr("index",index);
                        $("ul#wemos [index="+index+"]").attr("mac_address",light.mac_address);
                        $("ul#wemos [index="+index+"] a[var=name]").attr("mac_address",light.mac_address);
                        $("ul#wemos [index="+index+"]").attr("type",light.type);
                        $("ul#wemos [index="+index+"]").attr("subtype",light.subtype);
                        $("[collection=lights] [mac_address="+light.mac_address+"]").attr("state",light.state);
                        $("[collection=lights] [mac_address="+light.mac_address+"]").attr("target_state",light.target_state);
                        $("[collection=lights] [mac_address="+light.mac_address+"]").attr("lock_state",light.lock_state);
                        $("[collection=lights] [mac_address="+light.mac_address+"]").attr("error",light.error);
                        $("ul#wemos [index="+index+"] [var=name]").html(light.name);
                        //$("ul#wemos [index="+index+"] a[var=name]").attr("href","http://"+light.url+":"+light.port+"/setup.xml");
                        
                    })
                });    
            });
        }
    }
    refresh(){
        if(this.debug) console.log("WeMoListView::Refresh");
        this.display();
    }
}