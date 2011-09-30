<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/meta_entities.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'meta_entities';
$options['title'] = getConst(_BIZ_TOP_ENTITIES);
$options['facets'] =
    array('entity_GL' => 10, 'entity_ON' => 10, 'entity_PN' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>