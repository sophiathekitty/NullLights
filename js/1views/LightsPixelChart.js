/**
 * view for displaying pixel charts of when the lights are on or off.
 */
class LightsPixelChart extends View {
    constructor(debug = false){
        super(new LightsHourlyChart(debug),
            new Template("lights_pixel","/plugins/NullLights/templates/chart.html"),
            null,
            90000,debug);
    }
    build(room_id){
        if(this.debug) console.info("LightsPixelChart::Build(room_id)",room_id);
        if(this.template){
            this.template.getData(html=>{
                this.model.room(room_id,json=>{
                    if(json != null && 'charts' in json && json.charts.length > 0){
                        $(html).appendTo("#floors [room_id="+room_id+"] .charts").attr("room_id",room_id);
                        json.charts.forEach(chart=>{
                            if(this.debug) console.log("LightsPixelChart::Build",room_id,chart);
                            $("<div class='light' mac_address='"+chart.light.mac_address+"'></div>").appendTo("#floors [room_id="+room_id+"] .charts [collection=lights_chart] .hour");
                            this.displayRoomChart(room_id,chart);
                        });
                    }
                    //this.display(room_id);
                });
            });
        }
    }
    refresh(room_id){
        if(this.debug) console.info("LightsPixelChart::Refresh(room_id)",room_id);
        this.display(room_id);
    }
    display(room_id){
        if(this.debug) console.info("LightsPixelChart::Display(room_id)",room_id);
        if(this.model){
            this.model.room(room_id,json=>{
                if(json != null && 'charts' in json){
                    json.charts.forEach(chart=>{
                        if(this.debug) console.log("LightsPixelChart::Display",room_id,chart);
                        this.displayRoomChart(room_id,chart);
                    });
                }
            });
        }
    }
    displayRoomChart(room_id,chart){
        if(this.debug) console.info("LightsPixelChart::DisplayRoomChart",room_id,chart);
        for(var i = 0; i < 24; i++){
            if(this.debug) console.log("LightsPixelChart::DisplayRoomChart -- hourly",chart.hourly[i],chart.hourly[i].hour,chart.hourly[i].color);        
            $("#floors [room_id="+room_id+"] .charts [collection=lights_chart] .hour[hour="+chart.hourly[i].hour+"] [mac_address="+chart.light.mac_address+"]").css("background-color",chart.hourly[i].color);
        }
        /*
        chart.hourly.forEach(hour=>{
            $("#floors [room_id="+room_id+"] .charts [collection=lights_chart] .hour[hour="+hour.hour+"] [mac_address="+chart.light.mac_address+"]").css("background-color",hour.color);
        });
        */
    }
}