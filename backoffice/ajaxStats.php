<?php
	function dateFR($timestamp) {
		$jours							= array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
		$mois							= array('janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre');
		$jours_numero					= date('w', $timestamp);
		$jours_complet					= $jours[$jours_numero];
		$NumeroDuJour					= date('j', $timestamp);
		$suffixe						= ($NumeroDuJour==1) ? 'er' : '';
		$mois_numero					= date('n', $timestamp) -1;
		$mois_complet					= $mois[$mois_numero];
		$annee							= date('Y', $timestamp);
		return $NumeroDuJour.$suffixe.' '.$mois_complet.' '.$annee;
	}
	
	function dateadd($per,$n,$d) {
	   switch($per) {
	      case "yyyy": $n*=12;
	      case "m":
	         $d=mktime(date("H",$d),date("i",$d)
	            ,date("s",$d),date("n",$d)+$n
	            ,date("j",$d),date("Y",$d));
	         $n=0; break;
	      case "ww": $n*=7;
	      case "d": $n*=24;
	      case "h": $n*=60;
	      case "n": $n*=60;
	   }
	   return $d+$n;
	}
	
	function heureEte($dDate) {
		$heure_Ete = 0;
		$summerDay = "";
		$winterDay = "";
		$year = date('Y', $dDate);
		for($nDay = 31; $nDay>1; $nDay--) {
			$equinoxeEte = mktime(0, 0, 0, 3, $nDay, $year);
			if(date("w", $equinoxeEte) == 1 && date("h", $dDate) > 2 ) {
				$summerDay = $equinoxeEte;
				break;
			}
		}
		for($nDay = 31; $nDay>1; $nDay--) {
			$equinoxeHiver = mktime(0, 0, 0, 9, $nDay, $year);
			if(date("w", $equinoxeHiver) == 1 && date("h", $dDate) > 2 ) {
				$winterDay = $equinoxeHiver;
				break;
			}
		}
		if($dDate >= $summerDay && $dDate < $winterDay) $heure_Ete = 1;
		return $heure_Ete;
	}
	
	// Initialize system
	require_once dirname(__FILE__).'/initWebApp.php';
	
	// Get current project
	$project							= wcmProject::getInstance();
	
	// Open current session
	$session = wcmSession::getInstance();
	$html								= '<table width="100%">' . "\n";
	$response							= '';
	$day								= '';
	$dayRef								= '';
	$time								= '';
	$timezone							= '';
	$loctime							= '';
	$strWhen							= '';
	$divId								= 'divContent';
	$options	=	array	("en" => array("result_is_empty" => "No result for this selection !"
										),
							"fr" => array("result_is_empty" => "Aucun résultat pour cette sélection !"
										)
							);
	$lang								= getArrayParameter($_REQUEST, "lang", null);
	$id									= getArrayParameter($_REQUEST, "id", null);
	$date_range							= getArrayParameter($_REQUEST, "range", null);
	switch(substr($date_range.' ',0,5)) {
		case "today":
			$strWhen					= " AND DATE_FORMAT(startDate,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') ";
			break;
		case "curre":
			$strWhen					= " AND DATE_FORMAT(startDate,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m') ";
			break;
		case "week ":
			$strWhen					= " AND DATE_FORMAT(startDate,'%Y-%m-%d') >= DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -7 DAY),'%Y-%m-%d') ";
			break;
		case "month":
			$strWhen					= " AND DATE_FORMAT(startDate,'%Y-%m-%d') >= DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -30 DAY),'%Y-%m-%d') ";
			break;
		case "lastm":
			$strWhen					= " AND DATE_FORMAT(startDate,'%Y-%m') = DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -1 MONTH),'%Y-%m') ";
			break;
		case "range":
			$pieces = explode("|", $date_range);
			if($pieces[0]=="range") {
				$debut					= $pieces[1];
				$fin					= $pieces[2];
				if($debut!="") $strWhen	.= " AND DATE_FORMAT(startDate,'%Y-%m-%d') >= '".$debut."'";
				if($fin!="") $strWhen	.= " AND DATE_FORMAT(startDate,'%Y-%m-%d') <= '".$fin."'";
			}
	}
	$config								= wcmConfig::getInstance();

	$db									= new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));
	$query								= "select timezone from wcm_user where id = '".$id."'";
	$result								= mysql_query($query);
	while($row = mysql_fetch_assoc($result)) {
		$timezone						= $row['timezone'];
	}
	$query								= "select startDate from wcm_session where userId = '".$id."'".$strWhen." order by UNIX_TIMESTAMP(startDate) desc";
	$result								= mysql_query($query);

	if(mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)) {
			setlocale(LC_TIME, "fr_FR", "fr_FR@euro", "fr", "FR", "fra_fra", "fra");
			$dDate						= $row['startDate'];
			$day						= ($lang == 'fr') ? dateFR(strtotime($dDate)) : date ("F jS, Y", strtotime($dDate));
			$time						= date ('H \h i', strtotime($dDate));
			$loctime					= date ('H \h i', dateadd('h', $timezone + heureEte(strtotime($dDate)), strtotime($dDate)));
			$html						.= ($day != $dayRef && $dayRef!="") ? '<tr><td colspan="2"><hr/></td></tr>' : '';
			$html						.= ($day != $dayRef) ? '<tr>'."\n".'<td style="width:200px;font-weight:bold">'.$day.'</td>' : '<tr><td>&nbsp;</td>';
			$html						.= '<td>'.$loctime.' ('.$time.' GMT)</td>'."\n".'</tr>' . "\n";
			$dayRef						= $day;
		}
	} else {
		$html							.= '<tr><td>'.$options[$lang]["result_is_empty"].'</td>';
	}
	$html								.= '</table>' . "\n";
	
	$response							= '<response type="item" id="' . $divId . '"><![CDATA[' . $html .']]></response>'. "\n";
	
	// No browser cache
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
	
	// Xml output
	header("Content-Type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
	
	// Write ajax response
	echo '<ajax-response>' . "\n";
	echo $response;
	echo '</ajax-response>'. "\n";
?>