<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/meta_tags.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'meta_tags';
$options['title'] = getConst(_BIZ_TOP_EDITORIAL_TAGS);
$options['facets'] = array('tag' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);        $id = 'meta_tags';
?>