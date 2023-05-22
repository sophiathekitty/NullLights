class LightsCollection extends Collection {
    static instance = new LightsCollection();
    static debug_lights = true;
    constructor(debug = LightsCollection.debug_lights){
        if(debug) console.log("LightsCollection::Constructor");
        /**
         * Refactoring: need to update this to use the light groups instead of wemo
         */
        super("lights","light","/plugins/NullLights/api/light","/plugins/NullLights/api/light/save","id","collection_",debug);
        this.pull_delay = 0;
    }
    /**
     * get the lights for a room
     * @param {int} room_id 
     * @param {function(JSON)} callBack 
     */
    getRoomLights(room_id,callBack){
        this.getData(json=>{
            var lights = Array();
            json.lights.forEach(light=>{
                if(light.room_id == room_id) lights.push(light);
            });
            callBack(lights);
        });
    }
    /**
     * set the target state of the light to the opposite of the current state
     * @param {string} light_id 
     * @param {function(JSON)} callBack 
     * @param {function(*)} errorCallback 
     * @param {function(*)} doneCallback 
     */
    static toggleLight(light_id,callBack,errorCallback,doneCallback){
        /**
         * Refactoring: need to update this to use the light groups instead of wemo
         */
        if(this.debug) console.info("LightsCollection::ToggleLight",light_id);
        LightsCollection.instance.getItem(light_id,light=>{
            console.log("LightsCollection::ToggleLight:getItem",light_id,light);
            if(Number(light.state) == 0){
                light.target_state = 1;
            } else {
                light.target_state = 0;
            }
            if(this.log) console.log("LightsCollection::ToggleLight:getItem",light_id,light.state,"->",light.target_state);
            LightsCollection.instance.setItem(light,doneCallback);
            //LightsCollection.instance.pushData(callBack,errorCallback,errorCallback,doneCallback);
        });
    }
    /**
     * set the target state of the light to a specific target state
     * @param {string} light_id 
     * @param {int} state 
     * @param {function(JSON)} callBack 
     * @param {function(*)} errorCallback 
     */
    static setLightState(light_id,state,callBack,errorCallback){
        /**
         * Refactoring: need to update this to use the light groups instead of wemo
         */
        LightsCollection.instance.getItem(light_id,light=>{
            light.target_state = state;
            LightsCollection.instance.setItem(light,callBack);
            //LightsCollection.instance.pushData(callBack,errorCallback,errorCallback);
        });
    }
}