<?php
/**
 * @file
 * Drush file for d2f2 module.
 */

/**
 * hook_drush_command()
 */
function d2f2_drush_command() {

    $items['export-permissions'] = array(
        'description' => t('Export users permissions'),
    );

    $items['export-settings'] = array(
        'description' => t('Export drupal configuration'),
        'arguments' => array(
            'config' => 'Drupal config available : account, site',
        )
    );

    return $items;
}

/**
 * Callback de la commande drush 'export-permissions'
 */
function drush_d2f2_export_permissions() {

    _module_name_exist();

    $response = _dump_permissions(PERMISSIONS_FILE);
    _print_drush_log($response);
}

/**
 * Callback de la commande drush 'export-settings'
 * @param array $configs
 */
function drush_d2f2_export_settings($configs = array()) {

    _module_name_exist();
    if(empty($configs)) {
        return drush_log('You must specify your configs (separate by comma) --help to see available arguments', 'error');
    }

    foreach(explode(',', $configs) as $config) {
        $response = '';
        switch($config) {
            case 'account':
                $response = _dump_account_settings(USER_ACCOUNT_SETTINGS_FILE);
                break;
            case 'site':
                $response = _dump_site_settings(USER_ACCOUNT_SETTINGS_FILE);

                break;
            default:
                drush_log('Unable to find this config ' . $config, 'error');
                break;


        }
        _print_drush_log($response);
    }
}


/**
 * @return mixed
 */
function _module_name_exist() {
    $module_name = variable_get('deployment_module_name');
    if(!isset($module_name)) {
        return drush_set_error('You must specify the name of your deployment module.');
    }
}