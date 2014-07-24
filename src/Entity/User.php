<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mkeromen
 * Date: 2014-07-10
 * Time: 14:52
 * To change this template use File | Settings | File Templates.
 */

/**
 * Récupérer le rôle ID par le nom de rôle
 * @param $role_name
 * @return mixed
 */
function _get_rid_by_role_name($role_name) {

    $rid = db_select('role', 'r')
        ->fields('r', array('rid'))
        ->condition('name', $role_name, '=')
        ->execute()
        ->fetchField();

    return $rid;
}