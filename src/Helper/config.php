<?php
/**
 * ****************************************************
 * Helper de déploiement pour la gestion de la configuration Drupal
 * ****************************************************
 */

/**
 * Permet de récupérer le texte depuis un fichier de config
 * pour setup les emails
 * @param $key
 * - La clé représente la section du fichier .ini
 * @param $mail_file_path
 * - Le path vers le fichier de settings des emails
 *
 * @return mixed
 */
function _config_get_user_mail_register_text($key, $mail_file_path) {
    $user_register_ini_content = parse_ini_file($mail_file_path, TRUE);
    return $user_register_ini_content[$key];
}

/**
 * Permet de mettre à jour les alias que l'on a pu créer
 * @param $alias_path
 * @param $language
 */
function _config_update_alias($alias_path, $language) {

    $alias_ini_content = parse_ini_file($alias_path);
    foreach($alias_ini_content as $existing => $new_alias) {
        $path = array();
        $path['source'] = $existing;
        $path['alias'] = $new_alias;
        $path['language'] = $language;
        if(module_exists('path'))
            path_save($path);
        else
            echo 'Mise à jour des alias impossible.';
    }
}

/**
 * Permet d'ajouter un pattern de PathAuto et de regénérer les alias
 * @param string $nodeType (avec des underscores !)
 * @param string $pattern
 * @todo    Filtrer les alias à regénérer (node/*, user/* ou taxonomy/term/*) et dans le cas de node, filtrer le node->type
 */
function _config_new_pathauto_pattern($nodeType = 'node', $pattern) {

    module_load_include('inc', 'pathauto');
    module_load_include('inc', 'pathauto.pathauto');

    /* On créer notre nouveau motif */
    if($nodeType == 'node'){
        variable_set('pathauto_node_pattern', $pattern);
    }else{
        variable_set('pathauto_node_' . $nodeType . '_pattern', $pattern);
    }

    /* Suppression des alias existants */
    db_delete('url_alias')
        ->condition('source', 'node/%', 'LIKE')
        ->execute();

    /* On regenere les alias */
    $nids = db_query("SELECT nid FROM node")->fetchCol();
    pathauto_node_update_alias_multiple($nids, 'bulkupdate');
}
