<?php
define('APP_CONFIG', dirname(__DIR__) . '/../app/config.inc');

function _key_in_file($entries, $search_key) {

    foreach($entries as $entry) {
        if(strstr($entry, $search_key) !== FALSE) {
            return true;
        }
    }
    return false;
}

/*
function _write_app_config($search_key, $new_value) {

    $config_content = file_get_contents(APP_CONFIG);
    $entries = trim(str_replace(array('<?php', '?>'), '', $config_content));
    $entries = explode(PHP_EOL, $entries);

    if(!_key_in_file($entries, $search_key)) {
        $entry = "define('" . $search_key . "', '" . $new_value . "');";
        $entries[] = $entry;
    }

    $const = implode(PHP_EOL, $entries);
    $new_content = <<<EOF
<?php
$const
?>
EOF;

    _write_in_file(APP_CONFIG, $new_content);
}*/

/**
 * _create_directory()
 * @param $directory_name
 * @return bool
 */
/*
function _create_directory($directory_name) {
    $is_created = false;
    $config_path = variable_get('deployment_module_path') . DIRECTORY_SEPARATOR . $directory_name;
    if(!is_dir($config_path)) {
        $is_created = mkdir($config_path);
    } else {
        $is_created = true;
    }
    return $is_created;
}*/

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