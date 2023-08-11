/**
 * controller for the light profile editor
 */
class LightProfileEditorController extends Controller {
    static instance = new LightProfileEditorController();
    /**
     * the constructor
     * @param {boolean} debug 
     */
    constructor(debug = true){
        if(debug) console.info("LightProfileEditorController::constructor");
        super(new LightingProfileEditor(debug),debug);
        this.debug = debug;
    }
    /**
     * setup the event handling
     */
    ready(){
        if(this.debug) console.info("LightProfileEditorController::ready");
        /**
         * show the light profile editor
         */
        this.click("body",".light_profile_nav [action=create]",e=>{
            if(this.debug) console.debug("LightProfileEditorController::body .light_profile_nav [action=create]::Click");
            e.preventDefault();
            this.view.show($(e.currentTarget).attr("room_id"));
        });
        /**
         * load a profile into the editor
         */
        this.click("body",".light_profile_item [action=edit]",e=>{
            if(this.debug) console.debug("LightProfileEditorController::body .light_profile_item [action=edit]::Click",$(e.currentTarget).attr("profile_id"));
            e.preventDefault();
            this.view.show($(e.currentTarget).attr('room_id'),$(e.currentTarget).attr("profile_id"));
        });
        /**
         * save the profile
         */
        this.click("body","dialog#lighting_profile_editor [action=save]",e=>{
            if(this.debug) console.debug("LightProfileEditorController::body dialog.lighting_profile_editor [action=save]::Click");
            e.preventDefault();
            var data = {};
            data.id = $("dialog#lighting_profile_editor").attr("lighting_profile_id");
            data.name = $("dialog#lighting_profile_editor #light_profile_name").val();
            data.type = $("dialog#lighting_profile_editor #light_profile_type").val();
            data.light_level_min = $("dialog#lighting_profile_editor #light_profile_light_level_min").val();
            data.light_level_max = $("dialog#lighting_profile_editor #light_profile_light_level_max").val();
            data.room_id = $("dialog#lighting_profile_editor").attr("room_id");
            // add the devices to the profile
            data.devices = [];
            $("dialog#lighting_profile_editor .profile_devices select").each((i,e)=>{
                var device = {};
                device.id = $(e).attr("profile_device_id");
                device.profile_id = data.id;
                device.light_id = $(e).attr("light_id");
                device.state = $(e).val();
                data.devices.push(device);
            });
            this.view.model.saveProfile(data,json=>{
                if(this.debug) console.log("LightProfileEditorController::model.save - Complete",json);
                this.view.refreshProfileList(data.room_id);
                this.view.hide();
            },e=>{
                // get json from responseText
                var json = JSON.parse(e.responseText);
                if(this.debug) console.error("LightProfileEditorController::model.save - Error",json);
                $("dialog#lighting_profile_editor .error_message").html(json.error);
            });
        });
        /**
         * cancel the profile editor
         */
        this.click("body","dialog#lighting_profile_editor [action=cancel]",e=>{
            if(this.debug) console.debug("LightProfileEditorController::body dialog.lighting_profile_editor [action=cancel]::Click");
            e.preventDefault();
            this.view.hide();
        });
        /**
         * delete the profile
         */
        this.click("body","dialog#lighting_profile_editor [action=delete]",e=>{
            if(this.debug) console.debug("LightProfileEditorController::body dialog.lighting_profile_editor [action=delete]::Click");
            e.preventDefault();
            this.view.model.deleteProfile($("dialog#lighting_profile_editor").attr("lighting_profile_id"),json=>{
                if(this.debug) console.log("LightProfileEditorController::model.delete - Complete",json);
                this.view.refreshProfileList($("dialog#lighting_profile_editor").attr("room_id"));
                this.view.hide();
            },e=>{
                if(this.debug) console.error("LightProfileEditorController::model.delete - Error",e);
            });
        });
    }
}
