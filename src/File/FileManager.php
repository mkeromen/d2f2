<?php
define('OPENING_ERROR', 'Error occurred in opening file %s !');
define('WRITING_ERROR', 'Error occurred in writing file %s !');
define('WRITTING_SUCCESS', 'File %s has been correctly written !');

/**
 * Écrit des data dans un fichier JSON.
 * Prise en charge des logs drush si executé depuis une commande.
 * @param $file
 * @param $content
 * @param $mode
 */
function _write_json_file($file, $data, $mode = 'w') {

    $content = json_encode($data, JSON_PRETTY_PRINT);

    $fhandler = @fopen($file, $mode);
    if(!$fhandler) {
        $log = _prepare_log(sprintf(OPENING_ERROR, $file), 'error');
    } else {
        if(@fwrite($fhandler, $content) === false) {
            $log = _prepare_log(sprintf(WRITING_ERROR, $file), 'error');
        }
        @fclose($fhandler);

        $log = _prepare_log(sprintf(WRITTING_SUCCESS, $file), 'success');
    }

    return $log;
}


function _read_json_file($file, $mode = 'r') {

    $content = '';
    $fhandler = fopen($file, $mode);
    if(!$fhandler) {
        // TODO: Implements Log
    } else {
        while(!feof($fhandler)) {
            $content .= fgets($fhandler);
        }
        fclose($fhandler);
    }
    return json_decode($content, true);
}

?>