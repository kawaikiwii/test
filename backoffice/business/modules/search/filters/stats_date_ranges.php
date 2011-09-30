<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/stats_date_ranges.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'stats_date_ranges';
$options['title'] = getConst(_BIZ_DATE_RANGES);
$options['facets'] = array('modifiedAt' => 10); // TODO make configurable
$options['sort'] = false;

wcmModule('business/search/filters/abstract', $options);
?>