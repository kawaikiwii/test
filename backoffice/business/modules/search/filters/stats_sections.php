<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/stats_sections.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'stats_sections';
$options['title'] = getConst(_BIZ_SECTIONS);
$options['facets'] = array('channelId' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>