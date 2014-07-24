<?php

/**
 * Dump les configurations du site dans un fichier json.
 * @param $file
 */
function _dump_site_settings($file) {

    $site_settings = _get_variable_by_prefix('site_');
    return _write_json_file($file, $site_settings);
}

/**
 * Dump les configurations du compte dans un fichier json.
 */
function _dump_account_settings($account_file) {

    $account_settings = _get_variable_by_prefix('user_');
    return _write_json_file($account_file, $account_settings);
}

/**
 * Dump les permissions par rôle dans un fichier json.
 * @param $file
 * @return string
 */
function _dump_permissions($file) {

    $roles = db_select('role', 'r')
        ->fields('r', array('rid', 'name'))
        ->execute()
        ->fetchAllAssoc('rid');

    $permissions = db_select('role_permission', 'rp')
        ->distinct()
        ->fields('rp', array('permission'))
        ->execute()
        ->fetchCol();

    $permissions_to_write = array();
    foreach($roles as $rid => $role) {

        $permissions_by_role = db_select('role_permission', 'rp')
            ->fields('rp', array('permission'))
            ->condition('rid', $rid)
            ->execute()
            ->fetchCol();

        $permissions_diff = array_diff($permissions, $permissions_by_role);
        foreach($permissions_by_role as $key => $permission) {
            $permissions_to_write[$role->name][$permission] = 1;
        }

        foreach($permissions_diff as $key => $permission) {
            $permissions_to_write[$role->name][$permission] = 0;
        }
    }

    return _write_json_file($file, $permissions_to_write);
}

