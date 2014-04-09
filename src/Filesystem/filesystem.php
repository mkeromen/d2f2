<?php
define('APP_CONFIG', dirname(__DIR__) . '/../app/config.inc');

function _write_app_config($search_key, $new_value) {

    $config_content = file_get_contents(APP_CONFIG);
    $strip_content = str_replace(array('<?php', '?>'), '', $config_content);
    //var_dump($strip_content);

    $entry = "define('" . $search_key . "', '" . $new_value . "');";
    if(!empty($strip_content)) {
        //var_dump('non empty');
        $configs = explode(';', $strip_content);
        foreach($configs as $item_key => $item) {

            if(strstr($item, $search_key) !== FALSE) {
                $configs[$item_key] = $entry;
                array_pop($configs);
            }
        }
    } else {
        //var_dump('Else');
        $configs[] = $entry;
    }

    $new_content = '<?php ' . implode(PHP_EOL, $configs) . ' ?>';

    //var_dump($new_content);exit;

    _write_in_file(APP_CONFIG, $new_content);

}

/**
 * _create_directory()
 * @param $directory_name
 * @return bool
 */
function _create_directory($directory_name) {
    $is_created = false;
    $config_path = variable_get('deployment_module_path') . DIRECTORY_SEPARATOR . $directory_name;
    if(!is_dir($config_path)) {
        $is_created = mkdir($config_path);
    } else {
        $is_created = true;
    }
    return $is_created;
}

/**
 * _write_in_file()
 * @param $file
 * @param $content
 * @param $mode
 */
function _write_in_file($file, $content, $mode = 'w') {
    $fhandler = @fopen($file, 'w');
    if(!$fhandler) {
        drush_log('Error occurred in opening file ' . $file, 'error');
        exit;
    } else {
        if(@fwrite($fhandler, $content) === false) {
            drush_log('Error occurred in writing file ' . $file, 'error');
            exit;
        }
        @fclose($fhandler);
    }
}



?>