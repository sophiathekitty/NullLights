class WeMoListController extends Controller {
    constructor(debug = LightsCollection.debug_lights){
        if(debug) console.log("WeMoListController::Constructor");
        super(new WeMoListView(),debug);
    }
    ready(){
        if(this.debug){
            console.log("WeMoListController::Ready",$("ul#wemos").length);
        }
        //if($("ul#wemos").length == 0) return;
        this.view.build();
        this.interval = setInterval(this.refresh.bind(this),60000);
        this.click("ul#wemos","a",e=>{
            e.preventDefault();
            var mac_address = $(e.currentTarget).attr("mac_address");
            console.log("WemoListController::Click",mac_address,$(e.currentTarget).html());
            LightsCollection.toggleLight(mac_address,json=>{
                if(this.debug) console.log("WeMoListController::ToggleSuccess",json);
            },error=>{
                if(this.debug) console.error("WeMoListController::ToggleError",error);
            },json=>{
                if(this.debug) console.log("WeMoListController::ToggleDone",json);
                this.refresh();
            });
        });
    }
    refresh(){
        if(this.debug){
            console.log("WeMoListController::Refresh");
        }
        this.view.display();
    }
}