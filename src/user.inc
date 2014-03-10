<?php
/**
 * ****************************************************
 * Helper pour la gestion des utilisateurs dans Drupal
 * ****************************************************
 */

/**
 * Callback de la commande drush 'user-refresh-permissions'
 * Mise à jour du fichier permissions.json
 * @param $file
 * @return string
 */
function _user_refresh_permissions($file) {

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
 * Création d'utilisateur de base pour les rôles
 * webmestre / administrator par exemple
 * @param $name
 * @param $mail
 * @param $pass
 * @param $role_name
 */
function _user_create($name, $mail, $pass, $role_name) {
    $rid = _get_rid_by_name($role_name);
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
 * Création d'un compte et setup des permissions (modules)
 * pour la configuration des comptes sa + webmestre
 * @param $role_name
 * @param $weight
 * @param $file_access_path
 */
function _user_set_new_role($role_name, $weight, $file_access_path) {

    if(_create_role($role_name, $weight)) {
        //$rid = _get_rid_by_name($role_name);
        _set_user_permissions($role_name, $file_access_path);
    }
}


function _user_change_weight($role_name, $weight) {
    db_update('role')
        ->fields(array(
            'weight' => $weight
        ))
        ->condition('name', $role_name, '=')
        ->execute();
}
?>