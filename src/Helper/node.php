<?php
/**
 * ****************************************************
 * Helper de déploiement pour la gestion des nodes dans Drupal
 * ****************************************************
 */

/**
 * Création d'une node
 * pour le setup de page de configuration uniquement
 * comme la page d'accueil du site par exemple
 * @param $type
 * - Le machine name du type de contenu
 * @param $title
 * - Le titre de la node
 * @return mixed
 */
function _node_create($type, $title) {
    $node = new stdClass();
    $node->type = $type;
    $node->title = $title;
    $node->status = 1;
    $node->language = 'und';
    node_save($node);

    return $node->nid;
}

/**
 * Permet de mettre à jour les menus disponibles
 * depuis le type de contenu sélectionné
 * @param $type
 * - Le machine name du type de contenu
 * @param $menus
 * - Un array avec les machines name de menu
 */
function _node_add_menu_options($type, $menus) {
    variable_set('menu_options_' . $type, $menus);
}

function _hello_world() {
    return 'Hello world';
}


