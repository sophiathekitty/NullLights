class LightsCollection extends Collection {
    static instance = new LightsCollection(true);
    constructor(debug = false){
        if(debug) console.log("LightsCollection::Constructor");
        super("lights","light","/plugins/NullLights/api/light","/plugins/NullLights/api/light/save","mac_address","collection_",debug);
    }
    /**
     * get the lights for a room
     * @param {int} room_id 
     * @param {function(JSON)} callBack 
     */
    static getRoomLights(room_id,callBack){
        LightsCollection.instance.getData(json=>{
            var lights = Array();
            json.lights.forEach(light=>{
                if(light.room_id == room_id) lights.push(light);
            });
            callBack(lights);
        });
    }
    /**
     * set the target state of the light to the opposite of the current state
     * @param {string} mac_address 
     * @param {function(JSON)} callBack 
     * @param {function(*)} errorCallback 
     * @param {function(*)} doneCallback 
     */
    static toggleLight(mac_address,callBack,errorCallback,doneCallback){
        console.log("LightsCollection::ToggleLight",mac_address);
        LightsCollection.instance.getItem(mac_address,light=>{
            console.log("LightsCollection::ToggleLight:getItem",mac_address,light);
            if(light.state == 0){
                light.target_state = 1;
            } else {
                light.target_state = 0;
            }
            LightsCollection.instance.setItem(light);
            LightsCollection.instance.pushData(callBack,errorCallback,errorCallback,doneCallback);
        });
    }
    /**
     * set the target state of the light to a specific target state
     * @param {string} mac_address 
     * @param {int} state 
     * @param {function(JSON)} callBack 
     * @param {function(*)} errorCallback 
     */
    static setLightState(mac_address,state,callBack,errorCallback){
        LightsCollection.instance.getItem(mac_address,light=>{
            light.target_state = state;
            LightsCollection.instance.setItem(light);
            LightsCollection.instance.pushData(callBack,errorCallback,errorCallback);
        });
    }
}