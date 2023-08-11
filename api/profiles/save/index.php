<?php
require_once("../../../../../includes/main.php");
$data = [];
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $contents = file_get_contents("php://input");
    $profile = json_decode($contents,true);
    if(isset($_POST['name'])){
        // post data might be the json?
        $profile = $_POST;
    }
    $data['profile'] = $profile;
    $data['save'] = LightProfile::Save($profile);
    // return the error if there is one
    $error = "";
    if($data['save']['error'] != "") "profile:<br>".$error = $data['save']['sql']." || ".$data['save']['error']."<br>";
    foreach($data['save']['devices'] as $device){
        if($device['error'] != "") $error .= "device: ".$device['sql']." || ".$device['error']."<br>";
    }
    if($error != "") JsonError($error);
} else if($_SERVER['REQUEST_METHOD'] == "DELETE"){
    $contents = file_get_contents("php://input");
    $data['contents'] = $contents;
    $profile = json_decode($contents,true);
    if(isset($_POST['profile_id'])){
        // post data might be the json?
        $profile = $_POST;
    }
    if(isset($_GET['profile_id'])){
        // post data might be the json?
        $profile = $_GET;
    }
    if(is_null($profile) && strpos($contents,'=') > 0){
        // post data might be the json?
        $contents_parts = explode("=",$contents);
        $profile = ['profile_id'=>$contents_parts[1]];
    }
    $data['profile'] = $profile;
    $data['delete'] = LightProfile::Delete($profile);
    // return the error if there is one
    if($data['delete']['error'] != "") JsonError($data['delete']['error']);
    if($data['delete']['devices']['error'] != "") JsonError($data['delete']['devices']['error']);
} else {
    JsonError("Invalid request method",405);
}
OutputJson($data);
?>