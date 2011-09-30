<?php
/**
 * Project:     M
 * File:        modules/export/relaxTask/relaxTask.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */
	clearstatcache();
    $config = wcmConfig::getInstance();
 	$session = wcmSession::getInstance();
 	$language = $session->getLanguage();
	
	if (empty($language))
		$language = $config['wcm.default.language'];
 	$dir = $config['wcm.webSite.repository'].'docs/'.$language;
 	$lastmonth = date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d"), date("Y")));
 	echo "<table>";
 	echo "<tr>";
 	echo "<th>"._MENU_SYSTEM_ADMINISTRATION_DOCUMENTS."</th>";
 	echo "<th>"._MODIFICATION_DATE."</th>";
 	echo "</tr>";
 	if (is_dir($dir)) {
 		if ($dh = opendir($dir)) {
 			while (($file = readdir($dh)) !== false) {
 				if($file != "." && $file != "..") {
 					$date_modif = date("Y-m-d",filemtime($dir."/".$file));
 					$nouveau = "";
 					if($lastmonth < $date_modif)
 						$nouveau = "<span style='color:red'>("._NEW_DOCUMENT.")</span>";
 					echo "<tr><td><a href='".$config['wcm.webSite.urlRepository']."docs/".$language."/".$file."' target='_blank'>".$file."</a> ".$nouveau."</td><td style='text-align:center;'>".$date_modif."</td></tr><br />";
 				}
 			}
 		}
 	}
 	echo "</table>";
