<?php

function _restore_settings($file) {

    $settings = _read_json_file($file);
    foreach($settings as $key => $setting) {
        variable_set($key, $setting);
    }
}

function _restore_permissions($file, $role_name = '') {

    $permissions = _read_json_file($file);
    $rid = _get_rid_by_role_name($role_name);
    if($rid && isset($permissions[$role_name])) {
        user_role_change_permissions($rid, $permissions[$role_name]);
    }
}