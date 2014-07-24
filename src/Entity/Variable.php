<?php

/**
 * Récupère les variables selon un prefix donné
 * @param $prefix
 * @return array
 */
function _get_variable_by_prefix($prefix) {

    $variables = db_select('variable', 'v')
        ->fields('v', array('name'))
        ->condition('name', $prefix . '%', 'LIKE')
        ->execute()
        ->fetchCol();

    $data = array();
    foreach($variables as $variable) {
        $data[$variable] = variable_get($variable);
    }

    return $data;
}