/**
 * model for loading the rooms current lighting profile
 */
class CurrentLightProfileModel extends Model {
    constructor(debug = true){
        super("current_light_profile","/plugins/NullLights/api/profiles/current/","/plugins/NullLights/api/profiles/save/?DEBUG=verbose",0,"model_",debug);
    }
    /**
     * get the current light profile for a room
     * @param {int} room_id
     * @param {function} callBack
     * @returns {void}
     * @memberof CurrentLightProfileModel
     * @example
     * var model = new CurrentLightProfileModel();
     * model.getRoomProfile(1,json=>{
     *     console.log(json);
     * });
     */
    getRoomProfile(room_id,callBack){
        this.get_url = "/plugins/NullLights/api/profiles/current/";
        this.get_params = "?room_id="+room_id;
        this.getData(json =>{
            callBack(json);
        });
    }
    /**
     * get all room profiles
     * @param {int} room_id
     * @param {function} callBack
     */
    getAllRoomProfiles(room_id,callBack){
        this.get_url = "/plugins/NullLights/api/profiles/";
        this.get_params = "?room_id="+room_id;
        this.getData(json =>{
            callBack(json);
        });
    }
    /**
     * save a lighting profile
     * @param {json} json
     * @param {function} callBack
     * @param {function} errorCallBack
     */
    saveProfile(json,callBack,errorCallBack = null){
        if(this.debug) console.info("CurrentLightProfileModel::saveProfile",json);
        var debug = this.debug;
        $.ajax({
            url: this.save_url,
            type: "POST",
            data: json,
            dataType: "json",
            success: function (data) {
                if(debug) console.log("CurrentLightProfileModel::saveProfile::success",data);
                callBack(data);
            },
            error: function (xhr, status, error) {
                if(debug) console.error("CurrentLightProfileModel::saveProfile:error",xhr, status, error);
                if(errorCallBack) errorCallBack(xhr, status, error);
            }
        });
    }
    /**
     * delete a lighting profile
     * @param {int} profile_id
     * @param {function} callBack
     * @param {function} errorCallBack
     * @memberof CurrentLightProfileModel
     * @example
     * var model = new CurrentLightProfileModel();
     * model.deleteProfile(1,json=>{
     *    console.log(json);
     * },(xhr, status, error)=>{
     *   console.log(xhr, status, error);
     * });
     */
    deleteProfile(profile_id,callBack,errorCallBack = null){
        if(this.debug) console.info("CurrentLightProfileModel::deleteProfile",profile_id);
        $.ajax({
            url: this.save_url+"?profile_id="+profile_id,
            type: "DELETE",
            data: {profile_id:profile_id},
            dataType: "json",
            success: function (data) {
                if(this.debug) console.log("CurrentLightProfileModel::deleteProfile",data);
                callBack(data);
            },
            error: function (xhr, status, error) {
                if(this.debug) console.error("CurrentLightProfileModel::deleteProfile",xhr, status, error);
                if(errorCallBack) errorCallBack(xhr, status, error);
            }
        });
    }
    /**
     * get a lighting profile
     * @param {int} profile_id
     * @param {function} callBack
     * @returns {void}
     * @memberof CurrentLightProfileModel
     * @example
     * var model = new CurrentLightProfileModel();
     * model.getProfile(1,json=>{
     *    console.log(json);
     * });
     */
    getProfile(profile_id,callBack){
        if(this.debug) console.info("CurrentLightProfileModel::getProfile",profile_id);
        this.get_url = "/plugins/NullLights/api/profiles/";
        this.get_params = "?profile_id="+profile_id;
        this.getData(json =>{
            if(this.debug) console.log("CurrentLightProfileModel::getProfile",json);
            callBack(json);
        });
    }
    
}