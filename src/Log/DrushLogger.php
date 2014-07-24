<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mkeromen
 * Date: 2014-07-03
 * Time: 11:10
 * To change this template use File | Settings | File Templates.
 */

function _prepare_log($message, $type) {
    return array(
        'header'    => 'd2f2_log',
        'message'   => $message,
        'type'      => $type
    );
}

function _print_drush_log($data) {

    if(_is_log_array($data)) {
        drush_log($data['message'], $data['type']);
    }
}

function _is_log_array($data) {

    return (is_array($data) && isset($data['header']) && $data['header'] == 'd2f2_log');
}