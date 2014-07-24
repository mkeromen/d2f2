<?php
/**
 * ****************************************************
 * Helper de déploiement pour la gestion des utilisateurs dans Drupal
 * ****************************************************
 */



/**
 * Setup des permissions
 * @param $rid
 * @param $file_access_path
 */
function _user_set_permissions($role_name, $permissions_path) {
    $rid = _get_rid_by_role_name($role_name);
    $file_content_access = file_get_contents($permissions_path);
    $roles_access_to_active = json_decode($file_content_access, true);
    user_role_change_permissions($rid, $roles_access_to_active[$role_name]);
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
    $rid = _get_rid_by_role_name($role_name);
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




?>