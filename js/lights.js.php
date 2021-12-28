<?php
require_once("../../../includes/main.php");
// find all the css files in the plugin's folder
$js = CrawlMVC($root_path."plugins/NullLights/js/",[]);

function CrawlMVC($path,$js){
    //echo "$path\n";
    $shared_models_dir = opendir($path);
    // LOOP OVER ALL OF THE  FILES    
    while ($file = readdir($shared_models_dir)) { 
        //echo "$path$file\n";
        // IF IT IS NOT A FOLDER, AND ONLY IF IT IS A .php WE ACCESS IT
        if(!is_dir($file) && endsWith($file, '.js') && is_file($path.$file)) { 
            //require_once($path.$file);
            $js[] = $path.$file;
            //echo "included\n";
        } elseif(is_dir($path.$file) && $file != ".." && $file != "."){
            $js = CrawlMVC($path.$file."/",$js);
        }
    }
    // CLOSE THE DIRECTORY
    closedir($shared_models_dir);
    return $js;
}
//print_r($js);
sort($js);
if(isset($_GET['min'])){
    OutputJSFromFileListMin($js);
} else {
    OutputJSFromFileList($js);
}
?>