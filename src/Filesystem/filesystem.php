<?php
define('APP_CONFIG', dirname(__DIR__) . '/../app/config.inc');

function write_app_config() {
    //var_dump(APP_CONFIG);
    $config_content = file_get_contents(APP_CONFIG);
    $strip_content = str_replace(array('<?php', '?>'), '', $config_content);
    $configs = explode(';', $strip_content);


    print_r($configs);

    /*$configs = explode(';', $config_content);
    var_dump($configs);*/
}

?>