<?php
define('APP_CONFIG', dirname(__DIR__) . '/../app/config.inc');

function write_app_config($search_key, $new_value) {

    $config_content = file_get_contents(APP_CONFIG);
    $strip_content = str_replace(array('<?php', '?>'), '', $config_content);
    $configs = explode(';', $strip_content);

    foreach($configs as $item_key => $item) {
        if(strstr($item, $search_key) !== FALSE) {
            $configs[$item_key] = "define('" . $search_key . "', '" . $new_value . "')";
            array_pop($configs);
        }
    }

    var_dump($configs);

    $new_content = implode('', $configs);
    var_dump($new_content);

}

?>