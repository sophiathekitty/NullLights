/**
 * a view for editing a light profile
 * @extends View
 * @example
 * var view = new LightingProfileEditor();
 * view.show();
 * @example
 * var view = new LightingProfileEditor();
 * view.show(1);
 */
class LightingProfileEditor extends View {
    /**
     * the constructor
     * @param {boolean} debug
     * @memberof LightingProfileEditor
     */
    constructor(debug = true){
        super(
            new CurrentLightProfileModel(),
            new Template("LightingProfileEditor","/plugins/NullLights/widgets/room_light_profile_editor.php"),
            null,60000,debug);
    }
    build(){
        if(this.debug) console.warn("LightingProfileEditor::Build","missing room_id");
    }
    show(){
        if(this.debug) console.warn("LightingProfileEditor::Show","missing room_id");
    }
    /**
     *  build the lighting profile editor for new profile
     * @returns {void}
     * @memberof LightingProfileEditor
     */
    build(room_id){
        if(this.debug) console.info("LightingProfileEditor::Build");
        if(this.template == null) return;
        this.template.get_params = "?room_id="+room_id;
        this.template.getData(html=>{
            if(this.debug) console.log("LightingProfileEditor::Build",html);
            if(html == null) console.error("LightingProfileEditor::Build","html is null");
            else {
                $(html).appendTo("body");
                var dialog = document.getElementById('lighting_profile_editor');
                dialog.showModal();
                dialog.blur();    
            }
        });
    }
    /**
     * show the lighting profile editor for new profile
     */
    show(room_id){
        if(this.debug) console.info("LightingProfileEditor::Show");
        $("dialog#lighting_profile_editor").remove();
        this.build(room_id);
    }
    /**
     * show the lighting profile editor for existing profile
     * @param {int} room_id
     * @param {int} profile_id
     * @returns {void}
     * @memberof LightingProfileEditor
     */
    show(room_id,profile_id){
        if(this.debug) console.info("LightingProfileEditor::ShowExisting","room_id: "+room_id,"profile_id: "+profile_id);
        $("dialog#lighting_profile_editor").remove();
        this.build(room_id,profile_id);
    }
    /**
     * build the lighting profile editor for existing profile
     * @param {int} room_id
     * @param {int} profile_id
     * @returns {void}
     * @memberof LightingProfileEditor
     */
    build(room_id,profile_id){
        if(this.debug) console.info("LightingProfileEditor::Build","profile_id: "+profile_id,"room_id: "+room_id);  
        if(this.template == null) return;
        this.template.get_params = "?profile_id="+profile_id+"&room_id="+room_id;
        this.template.getData(html=>{
            if(this.debug) console.log("LightingProfileEditor::Build",html);
            if(html == null) console.error("LightingProfileEditor::Build","html is null");
            else {
                $(html).appendTo("body");
                var dialog = document.getElementById('lighting_profile_editor');
                dialog.showModal();
                dialog.blur();    
            }
        });
    }
    /**
     * close the lighting profile editor
     */
    hide(){
        if(this.debug) console.info("LightingProfileEditor::Hide");
        $("dialog#lighting_profile_editor").remove();
    }
    /**
     * refresh the lighting profiles list for a room
     * @param {int} room_id
     * @returns {void}
     * @memberof LightingProfileEditor
     */
    refreshProfileList(room_id){
        if(this.debug) console.info("LightingProfileEditor::RefreshProfileList","room_id: "+room_id);
        this.model.getAllRoomProfiles(room_id,profiles=>{
            if(this.debug) console.log("LightingProfileEditor::RefreshProfileList",profiles);
            if(profiles == null) console.error("LightingProfileEditor::RefreshProfileList","profiles is null");
            else {
                console.log("LightingProfileEditor::RefreshProfileList","removing existing profile list items",room_id,"[room_id="+room_id+"] ul.light-profiles li.light_profile_nav");
                $("div[room_id="+room_id+"] ul.light-profiles li.light_profile_item").remove();
                var target_li = $("div[room_id="+room_id+"] ul.light-profiles li.light_profile_nav");
                profiles.profiles.forEach(profile=>{
                    console.log("LightingProfileEditor::RefreshProfileList","adding profile list item",profile);
                    var profile_item = $("<li class='light_profile_item'><a href='#' action='edit' profile_id='"+profile.id+"' room_id='"+profile.room_id+"'>"+profile.name+"</a></li>");
                    target_li.before(profile_item);
                });
            }
        });
    }
}