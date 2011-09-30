<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/stats_sources.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'stats_sources';
$options['title'] = getConst(_BIZ_SOURCES);
$options['facets'] = array('source' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>