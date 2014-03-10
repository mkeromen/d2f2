<?php
/**
 * @file
 * Drush integration for the devel module.
 */

/**
 * Implements hook_drush_command()
 */
function deployment_helper_drush_command() {
    $items['refresh-permissions'] = array(
        'description' => dt('Update permissions to prepare deployment'),
        'arguments' => array(
            'module' => 'Name of your deployment module'
        )
    );

    return $items;
}

function drush_deployment_helper_refresh_permissions($module = '') {
    if(empty($module)) {
        return drush_set_error('You must specify the name of your deployment module');
    }
    $file = dirname(__DIR__ ) . '/' . $module . '/config/permissions.json';
    if(!is_file($file)) {
        return drush_set_error('You must have folder "config/permissions.json" in your deployment module');
    }

    $error = _user_refresh_permissions($file);
    if(empty($error)) {
        drush_print('File of permissions has been correctly updated !');
    } else {
        drush_set_error($error);
    }
}