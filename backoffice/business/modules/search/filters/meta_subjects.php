<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/meta_subjects.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call
$options['id'] = 'meta_subjects';
$options['title'] = getConst(_BIZ_TOP_SUBJECTS);
$options['facets'] = array('category' => 10); // TODO make configurable

wcmModule('business/search/filters/abstract', $options);
?>