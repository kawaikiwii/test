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
	
	$options	=	array	("en" => array("name" => "Name",
										"count" => "Connexions",
										"company" => "Company",
										"last_date" => "Last date"
										),
							"fr" => array("name" => "Nom",
										"count" => "Connections",
										"company" => "Groupe",
										"last_date" => "Dernière date"
										)
							);

	// Open current session
	$companyName						= getArrayParameter($_REQUEST, "companyName", null);
	$typeExport							= getArrayParameter($_REQUEST, "type", null);
	$date_range							= getArrayParameter($_REQUEST, "range", null);
	$lang								= getArrayParameter($_REQUEST, "lang", null);
	$html								= '';
//	$html								.= '<html>';
	$html								.= '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
	$html								.= '<head><title>Stats</title>';
	$html								.= '<meta http-equiv="Content-Type" content="application/x-excel" />';
	$html								.= '<meta http-equiv="Content-disposition" content="attachment; filename=stats.xls" />';
	$html								.= '<meta name="ProgId?" content="Excel.Sheet" />';
	$html								.= '</head><body>';
	$html								.= '<table>';
	$html								.= '<thead>';
	$html								.= '<tr><th width="200">' . $options[$lang]['name'] . '</th><th width="120">' . $options[$lang]['company'] . '</th><th width="120">' . $options[$lang]['count'] . '</th><th>' . $options[$lang]['last_date'] . '</th></tr>';
	$html								.= '</thead>';
	$html								.= '<tbody>';
	$response							= '';
	$strXml								= '';
	$userRef							= '';
	$count								= '';
	$strWhen							= '';
	$divId								= 'divContent';
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

	$db1		= new wcmDatabase(str_replace(":3306","",$config['wcm.businessDB.connectionString']));
	if($companyName == 'all') {
		$res_db1						= mysql_query("select wcmUserId, companyName from biz_account order by companyName");
	} else {
		$res_db1						= mysql_query("select * from biz_account where companyName='".$companyName."'");
	}
	$db2								= new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));

	while($row_db1	= mysql_fetch_object($res_db1)) {
		$strCompany						= $row_db1->companyName;
		$res_db2						= mysql_query("select U.id, replace(U.name,'|',' ') as name, U.timezone, count(S.userid) as cpte, case when max(startDate) != '' then max(startDate) else 'N/A' end as last_date from wcm_user U left join wcm_session S on(S.userid=U.id) where U.id='" . $row_db1->wcmUserId . "'" .$strWhen." group by U.id, U.name, U.timezone order by U.name");
		if(mysql_num_rows($res_db2) > 0) {
			while($row2 = mysql_fetch_assoc($res_db2)) {
				$userId					= $row2['id'];
				$dDate					= $row2['last_date'];
				$day					= ($lang == 'fr') ? dateFR(strtotime($dDate)) : date ("F jS, Y", strtotime($dDate));
				$time					= date ('H \h i', strtotime($dDate));
				$loctime				= date ('H \h i', dateadd('h', $row2['timezone'] + heureEte(strtotime($dDate)), strtotime($dDate)));
				//$html					.= '<tr><td>'.$row2['name'].'</td><td style="text-align:center; padding-right:10px">'. $row2['cpte'] . '</td><td>'.dateFR(strtotime($dDate)).' '.$loctime.' ('.$time.' GMT)</td></tr>';
				$html					.= '<tr><td>'.$row2['name'].'</td><td>'.$strCompany.'</td><td style="text-align:center; padding-right:10px">'. $row2['cpte'] . '</td><td>'.$dDate.' GMT</td></tr>';
				$strXml					.= '<user><name><![CDATA[' . $row2['name'] . ']]></name><company><![CDATA[' . $strCompany . ']]></company><count>'.$row2['cpte'].'</count><last_date>'.$dDate.' GMT</last_date></user>';
				$userRef				= $userId;
			}
		}
	}
	$html								.= '</tbody></table>';
	$html								.= '</body></html>';
	if($typeExport == "table") {
		$response						= '<response type="item" id="' . $divId . '"><![CDATA[' . $html .']]></response>'. "\n";
	} else {	//if($typeExport == "xml") {
		$response						= '<response type="item" id="' . $divId . '">' . $strXml .'</response>'. "\n";
	}
	
	if($typeExport == "table") {
		// Excel output
		// No browser cache
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
		header( 'Content-Type: application/vnd.ms-excel');
		header( 'Content-Type: application/x-excel');
		header( 'Content-disposition: attachment; filename="stats.xls"');
		echo "";
		echo $html;
	} else {
		// Xml output
		// No browser cache
		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
		header("Content-Type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		
		// Write ajax response
		echo '<ajax-response>' . "\n";
		echo $response;
		echo '</ajax-response>'. "\n";
	}
?>