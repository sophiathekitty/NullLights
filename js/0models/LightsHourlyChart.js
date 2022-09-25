/**
 * loads the hourly lights chart for individual rooms
 */
class LightsHourlyChart extends HourlyChart {
    constructor(debug = false){
        super("lights_logs","lights","lights_chart","/plugins/NullLights/api/log",debug);
    }
    /**
     * gets the room chart data
     * @param {int} room_id 
     * @param {function(JSON)} callBack 
     */
    room(room_id,callBack){
        this.get_params = "?room_id="+room_id;
        this.getData(json=>{
            if(json == null){
                if(this.debug) console.error("LightsHourlyChart::Room",room_id,json);
                return;
            }
            if('rooms' in json){
                if(this.debug) console.log("LightsHourlyChart::Room (rooms)",room_id,json);
                return;
                json.rooms.forEach(room=>{
                    if(Number(room.room_id) == room_id){
                        if(this.debug) console.log("LightsHourlyChart::Room (rooms) found",room_id,room);
                        callBack(room);
                    }
                });    
            } else if('charts' in json){
                if(this.debug) console.log("LightsHourlyChart::Room (charts)",room_id,json);
                callBack(json);
            } else {
                if(this.debug) console.error("LightsHourlyChart::Room (error)",room_id,json);
            }
        });
    }
}