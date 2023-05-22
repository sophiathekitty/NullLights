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
        this.refreshInterval(60000);
        this.click("main","a.light",e=>{
            e.preventDefault();
            /**
             * Refactoring: need to update this to use the light groups instead of wemo
             */
            var light_id = $(e.currentTarget).attr("light_id");
            console.log("RoomLightsController::Click",light_id,$(e.currentTarget).html());
            LightsCollection.toggleLight(light_id,json=>{
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
        this.view.refreshStates();
        //this.view.display();
    }
}