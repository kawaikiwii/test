<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
 /**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/

$userId = 0;
if (isset($CURRENT_USER)) {
	$userId = $CURRENT_USER.id ;
}

return array (

/** CSS **/

	'index.css' => array ('//inc/css/default/index-base.css', '//inc/css/default/index.css'),
	'home.css' => array ('//inc/css/default/home.css'),
	'afprelax.css'=> array ('//inc/css/ext-all.css', '//inc/css/reset.css', '//inc/css/default/ari-base.css', '//inc/css/default/ari-desk.css', '//inc/css/default/ari-item.css', '//inc/css/default/ari-sidebar.css', '//inc/css/default/ari-preview.css', '//inc/css/default/ari-search.css', '//inc/css/default/ari-dragdrop.css', '//inc/css/default/home.css'),

	'index.fra.css' => array ('//inc/css/ext-all.css','//inc/css/default/index-base.css', '//inc/css/default/fra/index.css'),
	'fra.css'=> array ('//inc/css/ext-all.css', '//inc/css/reset.css', '//inc/css/default/fra/base.css', '//inc/css/default/ari-base.css', '//inc/css/default/ari-desk.css', '//inc/css/default/ari-item.css', '//inc/css/default/ari-sidebar.css', '//inc/css/default/ari-preview.css', '//inc/css/default/ari-search.css', '//inc/css/default/ari-dragdrop.css', '//inc/css/default/home.css'),

	'index.fr.css' => array (/*'//inc/css/default/index-base.css',*/'//inc/css/default/fr/relax.css'),	
		
	//'index.fr.css' => array ('//inc/css/default/index-base.css', '//inc/css/default/fr/index.css'),
	'fr.css'=> array ('//inc/css/ext-all.css', '//inc/css/reset.css', '//inc/css/default/fr/base.css', '//inc/css/default/ari-base.css', '//inc/css/default/ari-desk.css', '//inc/css/default/ari-item.css', '//inc/css/default/ari-sidebar.css', '//inc/css/default/ari-preview.css', '//inc/css/default/ari-search.css', '//inc/css/default/ari-dragdrop.css', '//inc/css/default/home.css'),

	'index.en.css' => array (/*'//inc/css/default/index-base.css', */'//inc/css/default/en/relax.css'),
	//'index.en.css' => array ('//inc/css/default/index-base.css', '//inc/css/default/en/index.css'),
	'en.css'=> array ('//inc/css/ext-all.css', '//inc/css/reset.css', '//inc/css/default/en/base.css', '//inc/css/default/ari-base.css', '//inc/css/default/ari-desk.css', '//inc/css/default/ari-item.css', '//inc/css/default/ari-sidebar.css', '//inc/css/default/ari-preview.css', '//inc/css/default/ari-search.css', '//inc/css/default/ari-dragdrop.css', '//inc/css/default/home.css'),

/** JS **/
/** base **/
	'base.js' => array ('//inc/js/ga-tracker.js', '//inc/lib/ext2.2/adapter/ext/ext-base.js', '//inc/lib/ext2.2/ext-all.js', '//inc/lib/ext2.2/addons/ext.ux.storemenu.js', '//inc/lib/ext2.2/addons/ext.ux.columnnodeui.js', '//inc/js/ar-override.js'),
/** site specific **/
	'en.js'=> array ('//inc/lib/ext2.2/locale/ext-lang-en.js', '//sites/en/conf/arc.js', '//sites/en/conf/arl.js',  '//sites/en/conf/config.js', '//inc/js/ari.js', '//inc/js/ard.js', '//inc/js/are.js', '//inc/js/arr.js', '//inc/js/ar-onReady.js'),
	'fr.js'=> array ('//inc/lib/ext2.2/locale/ext-lang-fr.js', '//sites/fr/conf/arc.js', '//sites/fr/conf/arl.js',   '//sites/fr/conf/config.js', '//inc/js/ari.js', '//inc/js/ard.js', '//inc/js/are.js', '//inc/js/arr.js', '//inc/js/ar-onReady.js'),
	'fra.js'=> array ('//inc/lib/ext2.2/locale/ext-lang-fr.js', '//sites/fra/conf/arc.js', '//sites/fra/conf/arl.js',  '//sites/fra/conf/config.js',  '//inc/js/ari.js', '//inc/js/ard.js', '//inc/js/are.js', '//inc/js/arr.js', '//inc/js/ar-onReady.js'),
/** user **/
	'user.js' => array("//rp/users/$userId/conf.js")
);
