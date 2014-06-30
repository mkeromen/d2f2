<?php
/**
 * ****************************************************
 * Helper de déploiement pour la gestion des utilisateurs dans Drupal
 * ****************************************************
 */

/**
 * Callback de la commande drush 'user-refresh-permissions'
 * Mise à jour du fichier permissions.json
 * @param $file
 * @return string
 */
function _user_export_permissions($file) {

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

    $permissions_json = json_encode($permissions_to_write, JSON_PRETTY_PRINT);

    $error = '';
    $fhandler = @fopen($file, 'w');
    if(!$fhandler) {
        $error = 'Error occurred in open file';
    } else {
        if(fwrite($fhandler, $permissions_json) === false) {
            $error = 'Error occurred in writing file';
        }
        fclose($fhandler);
    }

    return $error;
}

/**
 * Setup des permissions
 * @param $rid
 * @param $file_access_path
 */
function _user_set_permissions($role_name, $permissions_path) {
    $rid = _user_get_rid_by_name($role_name);
    $file_content_access = file_get_contents($permissions_path);
    $roles_access_to_active = json_decode($file_content_access, true);
    user_role_change_permissions($rid, $roles_access_to_active[$role_name]);
}

/**
 * _user_export_account_settings()
 * Permet d'exporter les settings du compte utilisateur dans un fichier
 */
function _user_export_account_settings($account_file) {

    $account_settings = array(
        'user_basic_settings' => array(
            'user_register' => variable_get('user_register')
        )
    );

    $user_admin_role    = variable_get('user_admin_role');
    $user_cancel_method = variable_get('user_cancel_method');
    if(isset($user_admin_role)) {
        $account_settings['user_basic_settings']['user_admin_role'] = $user_admin_role;
    }
    if($user_cancel_method) {
        $account_settings['user_basic_settings']['user_cancel_method'] = $user_cancel_method;
    }

    $has_pictures = variable_get('user_pictures');
    if(isset($has_pictures)) {
        $account_settings['user_pictures_settings']['has_picture'] = $has_pictures;

        $picture_fields = db_select('variable', 'v')
            ->fields('v', array('name'))
            ->condition('name', 'user_picture__%', 'LIKE')
            ->execute()
            ->fetchCol();

        foreach($picture_fields as $picture_field) {
            $account_settings['user_pictures_settings'][$picture_field] = variable_get($picture_field);
        }
    }

    $mail_fields = db_select('variable', 'v')
        ->fields('v', array('name'))
        ->condition('name', '%user_mail%', 'LIKE')
        ->execute()
        ->fetchCol();

    foreach($mail_fields as $mail_field) {
        $account_settings['user_pictures_settings'][$mail_field] = variable_get($mail_field);
    }

    _write_in_file($account_file, json_encode($account_settings, JSON_PRETTY_PRINT));

    drush_log($account_file . ' has been written !', 'success');
}

function _user_import_account_settings($file_settings_path) {
    // TODO : Implements this
    //file_get_contents($file_settings_path);
}

/**
 * Création d'utilisateur de base pour les rôles
 * webmestre / administrator par exemple
 * @param $name
 * @param $mail
 * @param $pass
 * @param $role_name
 */
function _user_create($name, $mail, $pass, $role_name) {
    $rid = _user_get_rid_by_name($role_name);
    $fields = array(
        'name' => $name,
        'mail' => $mail,
        'pass' => $pass,
        'init' => $mail,
        'status' => 1,
        'roles' => array(
            $rid => $role_name
        )
    );

    user_save('', $fields);
}

/**
 * Création d'un rôle avec son poids
 * @param $name
 * @param $weight
 * @return boolean
 */
function _user_create_role($name, $weight) {
    $role = new stdClass();
    $role->name = $name;
    $role->weight = $weight;
    return user_role_save($role);
}

/**
 * Création d'un compte et setup des permissions (modules)
 * pour la configuration des comptes sa + webmestre
 * @param $role_name
 * @param $weight
 * @param $file_access_path
 */
function _user_set_new_role($role_name, $weight, $file_access_path) {

    if(_user_create_role($role_name, $weight)) {
        _user_set_permissions($role_name, $file_access_path);
    }
}

/**
 * Change le poids d'un rôle dans l'administration afin de gérer
 * l'héritage des droits
 * @param $role_name
 * @param $weight
 */
function _user_change_weight($role_name, $weight) {
    db_update('role')
        ->fields(array(
            'weight' => $weight
        ))
        ->condition('name', $role_name, '=')
        ->execute();
}

/**
 * Récupérer le rôle ID par le nom de rôle
 * @param $role_name
 * @return mixed
 */
function _user_get_rid_by_name($role_name) {
    $rid = db_select('role', 'r')
        ->fields('r', array('rid'))
        ->condition('name', $role_name, '=')
        ->execute()
        ->fetchField();

    return $rid;
}


?>