<?php
/**
 * @file
 * Drush integration for the devel module.
 */

/**
 * hook_drush_command()
 */
function d2f2_drush_command() {
    $items['export-permissions'] = array(
        'description' => t('Export users permissions in permissions.json file.'),
        'arguments' => array(
            'module' => 'Name of your deployment module'
        )
    );

    $items['export-config'] = array(
        'description' => t('Export Drupal config'),
        'arguments' => array(
            'config' => 'Name of Drupal config to export in file.ini.
            Delimit multiple using commas. Drupal config available : account',
        )
    );

    return $items;
}

function drush_d2f2_export_permissions($module = '') {
    if(empty($module)) {
        return drush_set_error('You must specify the name of your deployment module.');
    }
    $file = dirname(__DIR__ ) . '/' . $module . '/config/permissions.json';
    if(!is_file($file)) {
        return drush_set_error('You must have folder "config/permissions.json" in your deployment module.');
    }

    $error = _user_export_permissions($file);
    if(empty($error)) {
        drush_print('File of permissions has been correctly updated !');
    } else {
        drush_set_error($error);
    }
}

/**
 * Callback de la commande drush 'export-config'
 * @param array $configs
 * @return mixed
 */
function drush_d2f2_export_config($configs = array()) {
    if(empty($configs)) {
        return drush_log('You must specify your configs', 'error');
    }

    foreach(explode(',', $configs) as $config) {
        switch($config) {
            case 'account':
                _user_export_account_settings();
                break;
            default:
                drush_log('Unable to find this config ' . $config, 'error');
                break;
        }
    }
    //$configs = explode(',', $configs);
    //var_dump($configs);
}