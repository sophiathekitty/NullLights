/**
 * model for loading the rooms current lighting profile
 */
class CurrentLightProfileModel extends Model {
    constructor(debug = true){
        super("current_light_profile","/plugins/NullLights/api/profiles/current/","/plugins/NullLights/api/profiles/save/",0,"model_",debug);
    }
    getRoomProfile(room_id,callBack){
        this.get_params = "?room_id="+room_id;
        this.getData(json =>{
            callBack(json);
        });
    }
}