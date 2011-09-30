<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/meta_concepts.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'meta_concepts';
$options['title'] = getConst(_BIZ_TOP_CONCEPTS);
$options['facets'] = array('concept' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>