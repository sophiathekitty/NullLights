class RoomLightsController extends Controller {
    constructor(debug = LightsCollection.debug_lights){
        if(debug) console.log("RoomLightsController::Constructor");
        super(new RoomLightsView(),debug);
    }
    ready(){
        if(this.debug){
            console.log("RoomLightsController::Ready");
        }
    }
    roomsReady(){
        if(this.debug){
            console.log("RoomLightsController::RoomsReady");
        }
        //this.interval = setInterval(this.refresh.bind(this),20000);
        this.click("main","a.light",e=>{
            e.preventDefault();
            var mac_address = $(e.currentTarget).attr("mac_address");
            console.log("RoomLightsController::Click",mac_address,$(e.currentTarget).html());
            LightsCollection.toggleLight(mac_address,json=>{
                if(this.debug) console.log("RoomLightsController::ToggleSuccess",json);
            },error=>{
                if(this.debug) console.error("RoomLightsController::ToggleError",error);
            },json=>{
                if(this.debug) console.log("RoomLightsController::ToggleDone",json);
                this.view.refreshStates();
                LightsCollection.instance.pullData(json=>{
                    this.view.refreshStates();
                })
            });
        });
    }
    refresh(){
        if(this.debug){
            console.log("RoomLightsController::Refresh");
        }
        //this.view.display();
    }
}