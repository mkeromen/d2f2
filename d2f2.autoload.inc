<?php

_d2f2_autoload(__DIR__);

function _d2f2_autoload($path) {
    $all_files = scandir($path);
    $exclude_files = array('.', '..', '.git');
    $files = array_diff($all_files, $exclude_files);
    
    foreach($files as $file) {
        
        $file_path = $path . DIRECTORY_SEPARATOR . $file;
        if(is_dir($file_path)) {
            _d2f2_autoload($file_path);
        } else if($path !== __DIR__) {
               
            $file_info = pathinfo($file_path);
            if($file_info['extension'] == 'inc' || $file_info['extension'] == 'php') {
                require_once $file_path;
            }
        }
    }
}
?>
