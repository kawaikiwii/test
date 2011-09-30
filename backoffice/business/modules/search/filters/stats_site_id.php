<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/stats_site_id.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'stats_site_id';
$options['title'] = getConst(_BIZ_LANGUAGE);
$options['facets'] = array('className' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>