<?php
/**
 * Project:     WCM
 * File:        testWS.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system
require_once(dirname( __FILE__ ).'/initWebApp.php');

// Initialize variables
$lang								= getArrayParameter($_REQUEST, "lang", null);
if($lang == "")	$lang				= "en";
$userId								= getArrayParameter($_REQUEST, "id", null);
//$userName							= getArrayParameter($_REQUEST, "name", null);
$company							= getArrayParameter($_REQUEST, "company", null);
$managerId							= getArrayParameter($_REQUEST, "managerId", null);
$supervisor							= getArrayParameter($_REQUEST, "supervisor", null);
$typeExport							= getArrayParameter($_REQUEST, "typeExport", null);
if($company != "" || $company == "0") {
	$strType						= "company";
} else if($managerId != "" || $supervisor == "0" || $supervisor == "yes") {
	if($managerId != "") {
		$strType					= "supervisor";
	} else {
		$strType					= "supervisors";
	}
} else if($userId != "") {
	$strType						= "user";
} else {
	$strType						= "users";
}
$options	=	array	("en" => array("today" => "Today",
									"week" => "Last 7 days",
									"currentmonth" => "Current month",
									"month" => "Last 30 days",
									"lastmonth" => "Last month",
									"range" => "Range",
									"all" => "All",
									"start" => "Start",
									"end" => "End",
									"users" => "Users",
									"companies" => "Companies",
									"supervisors" => "Supervisors",
									"archives" => "Archives",
									"before" => "before"
									),
						"fr" => array("today" => "Aujourd'hui",
									"week" => "7 derniers jours",
									"currentmonth" => "Mois en cours",
									"month" => "30 derniers jours",
									"lastmonth" => "Mois dernier",
									"range" => "Votre sélection",
									"all" => "Tout",
									"start" => "Début",
									"end" => "Fin",
									"users" => "Utilisateurs",
									"companies" => "Groupes",
									"supervisors" => "Superviseurs",
									"archives" => "Archives",
									"before" => "avant"
									)
						);

// Execute special action
$action = $session->getCurrentAction();
wcmCache::clear();

if ($session->userId) {
	// Check permission on default site
	if (!$session->isAllowed($session->getSite(), wcmPermission::P_READ)) {
		$sites			= bizobject::getBizobjects('site');
		foreach ($sites as $site) {
			// Find first allowed website
			if ($session->isAllowed($site, wcmPermission::P_READ)) {
				$session->setSite($site);
			}
		}
		
		if ($session->getSiteId() == 0) {
			$session->logout();
			wcmMVC_Action::setError(_NO_SITE_ALLOWED);
		}
	}           
	
	// Reload previous action
	if (isset($_SESSION['wcm']['tmp']['previousActionBeforeTimeout'])) {
		$session->setCurrentAction($_SESSION['wcm']['tmp']['previousActionBeforeTimeout']);
		unset($_SESSION['wcm']['tmp']['previousActionBeforeTimeout']);
	}
}

// Load page for current action
$action								= $session->getCurrentAction();
$session->ping(); // Ping session to keep it active

//require_once(dirname( __FILE__ ).'/business/cron/init.php');
$config								= wcmConfig::getInstance();
//print_r($config);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
<html>
	<head>
		<title>Stats</title>
	    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	    <link rel="stylesheet" type="text/css" href="skins/main.css" />
		<?php include('js/main.js.php');?>
		<script type="text/javascript" language="JavaScript">
			var tabOptions=new Array();
			tabOptions["en"] = new Array();
			tabOptions["en"]["today"] = "Today";
			tabOptions["en"]["week"] = "Last 7 days";
			tabOptions["en"]["currentmonth"] = "Current month";
			tabOptions["en"]["month"] = "Last 30 days";
			tabOptions["en"]["lastmonth"] = "Last month";
			tabOptions["en"]["range"] = "Range";
			tabOptions["en"]["all"] = "All";
			tabOptions["en"]["start"] = "Start";
			tabOptions["en"]["end"] = "End";
			tabOptions["en"]["pick"] = "Pick";
			tabOptions["en"]["users"] = "Users";
			tabOptions["en"]["companies"] = "Companies";
			tabOptions["en"]["supervisors"] = "Supervisors";
			tabOptions["en"]["strCSV"] = "name\tcompany\tconnections\tlast date\n";
			tabOptions["fr"]=new Array()
			tabOptions["fr"]["today"] = "Aujourd'hui";
			tabOptions["fr"]["week"] = "7 derniers jours";
			tabOptions["fr"]["currentmonth"] = "Mois en cours";
			tabOptions["fr"]["month"] = "30 derniers jours";
			tabOptions["fr"]["lastmonth"] = "Mois dernier";
			tabOptions["fr"]["range"] = "Votre sélection";
			tabOptions["fr"]["all"] = "Tout";
			tabOptions["fr"]["start"] = "Début";
			tabOptions["fr"]["end"] = "Fin";
			tabOptions["fr"]["pick"] = "Sélectionner";
			tabOptions["fr"]["users"] = "Utilisateurs";
			tabOptions["fr"]["companies"] = "Groupes";
			tabOptions["fr"]["supervisors"] = "Superviseurs";
			tabOptions["fr"]["strCSV"] = "nom\tgroupe\tconnexions\tderniere date\n";

			Nom = navigator.appName; 
			ns = (Nom == 'Netscape') ? 1:0 
			ie = (Nom == 'Microsoft Internet Explorer') ? 1:0 

			var _xmlHttp			= null;
			var lang				= "<?php echo $lang ?>";
			var strRange			= "";
			var strType				= "<?php echo $strType ?>";
			
			// retourne un objet xmlHttpRequest.
			function getXMLHTTP(){
				var xhr				= null;
				if(window.XMLHttpRequest)			// Firefox, Opera et autres
					xhr				= new XMLHttpRequest();
				else if(window.ActiveXObject) {	// Internet Explorer
					try {
						xhr			= new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try {
							xhr		= new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e1) {
								xhr	= null;
						}
						}
					}
				else {
					alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
				}
				return xhr;
			}
			
			function change_statsContent(userID) {
				document.getElementById("statsContent").innerHTML="<div id='statsDivWait'><img src='skins/default/images/gui/loading3.gif' /></div>";
				if(_xmlHttp && _xmlHttp.readyState != 0){
					_xmlHttp.abort()
				}
				_xmlHttp			= getXMLHTTP();
				if(_xmlHttp) {
					if (document.getElementById('selDates').value.substr(0, 5) == "range") {
						strRange = 'range|' + document.getElementById('stats_starts').value + '|' + document.getElementById('stats_ends').value;
					} else {
						strRange = document.getElementById('selDates').value;
					}
					URL = "ajaxStats.php?id="+userID+"&lang="+lang+"&range="+strRange;
					_xmlHttp.open("GET", URL, true);
					_xmlHttp.onreadystatechange = function() {
						if(_xmlHttp.readyState == 4 && _xmlHttp.responseText) {
							var xmlDoc=_xmlHttp.responseXML.documentElement;
							document.getElementById("statsContent").innerHTML = xmlDoc.getElementsByTagName("response")[0].childNodes[0].nodeValue;
						}
					}
					_xmlHttp.send(null); // envoi de la requête
				}
			}
			
			function change_account(companyName, typeExport) {
				if(companyName != "") {
					if (typeExport == "xls") document.getElementById("statsContent").innerHTML="<div id='statsDivWait'><img src='skins/default/images/gui/loading3.gif' /></div>";
					if(_xmlHttp && _xmlHttp.readyState != 0){
						_xmlHttp.abort()
					}
					_xmlHttp			= getXMLHTTP();
					if(_xmlHttp) {
						if (document.getElementById('selDates').value.substr(0, 5) == "range") {
							strRange = 'range|' + document.getElementById('stats_starts').value + '|' + document.getElementById('stats_ends').value;
						} else {
							strRange = document.getElementById('selDates').value;
						}
						URL = "ajaxStatsCompany.php?companyName="+companyName+"&type=table&lang="+lang+"&range="+strRange;
						//window.open(URL, "stats2", "");
						_xmlHttp.open("GET", URL, true);
						_xmlHttp.onreadystatechange = function() {
							if (_xmlHttp.readyState == 4 && _xmlHttp.responseText) {
								var strCSV = tabOptions[lang]["strCSV"];
								if (typeExport == "table" || typeExport == "xls") {
									var htmlTable = _xmlHttp.responseText;
								}
								else {
									var xmlDoc = _xmlHttp.responseXML.documentElement;
									var tabUsers = xmlDoc.getElementsByTagName("user");
									for (var i = 0; i < tabUsers.length; i++) {
									 strCSV += tabUsers[i].childNodes[0].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[1].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[2].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[3].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[4].childNodes[0].nodeValue + "\n";
									 }
								}
								if (typeExport == "xls") {
									var fileName = (companyName == "") ? "stats.xls" : "stats_" + companyName + ".xls";
									win_Stats = window.open("about:blank", "stats", "top=100,left=100,height=800,width=1600,directories=no,location=no,toolbar=no,menubar=no,scrollbars=yes");
									if (win_Stats && win_Stats.top) {
										win_Stats.document.write(_xmlHttp.responseText);
										win_Stats.document.close();
										win_Stats.focus();
									} else {
										document.getElementById("statsContent").innerHTML = _xmlHttp.responseText;
										alert("Votre navigateur bloque les popups !");
									}
								} else  if (typeExport == "table") {
									document.getElementById("statsContent").innerHTML = htmlTable;
								} else {
									document.getElementById("statsContent").innerHTML = strCSV;
								}
							}
						}
						_xmlHttp.send(null); // envoi de la requête	
					}
				}
			}
			
			function change_supervisor(supervisorID, typeExport) {
				if(supervisorID != "") {
					if (typeExport != "xls") document.getElementById("statsContent").innerHTML="<div id='statsDivWait'><img src='skins/default/images/gui/loading3.gif' /></div>";
					if(_xmlHttp && _xmlHttp.readyState != 0){
						_xmlHttp.abort()
					}
					_xmlHttp			= getXMLHTTP();
					if(_xmlHttp) {
						if (document.getElementById('selDates').value.substr(0, 5) == "range") {
							strRange = 'range|' + document.getElementById('stats_starts').value + '|' + document.getElementById('stats_ends').value;
						} else {
							strRange = document.getElementById('selDates').value;
						}
						URL = "ajaxStatsManager.php?supervisorID="+supervisorID+"&type=table&lang="+lang+"&range="+strRange;
						//window.open(URL, "stats2", "");
						_xmlHttp.open("GET", URL, true);
						_xmlHttp.onreadystatechange = function() {
							if (_xmlHttp.readyState == 4 && _xmlHttp.responseText) {
								var strCSV = tabOptions[lang]["strCSV"];
								if (typeExport == "table" || typeExport == "xls") {
									var htmlTable = _xmlHttp.responseText;
								}
								else {
									var xmlDoc = _xmlHttp.responseXML.documentElement;
									var tabUsers = xmlDoc.getElementsByTagName("user");
									for (var i = 0; i < tabUsers.length; i++) {
									 strCSV += tabUsers[i].childNodes[0].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[1].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[2].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[3].childNodes[0].nodeValue;
									 strCSV += "\t" + tabUsers[i].childNodes[4].childNodes[0].nodeValue + "\n";
									 }
								}
								if (typeExport == "xls") {
									var fileName = (supervisorID == "") ? "stats.xls" : "stats_" + supervisorID + ".xls";
									win_Stats = window.open("about:blank", "stats", "top=100,left=100,height=800,width=1600,directories=no,location=no,toolbar=no,menubar=no,scrollbars=yes");
									if (win_Stats && win_Stats.top) {
										win_Stats.document.write(_xmlHttp.responseText);
										win_Stats.document.close();
										win_Stats.focus();
									} else {
										document.getElementById("statsContent").innerHTML = _xmlHttp.responseText;
										alert("Votre navigateur bloque les popups !");
									}
								} else  if (typeExport == "table") {
									document.getElementById("statsContent").innerHTML = htmlTable;
								} else {
									document.getElementById("statsContent").innerHTML = strCSV;
								}
							}
						}
						_xmlHttp.send(null); // envoi de la requête	
					}
				}
			}
			
			function reset_SelDates(){
				//document.getElementById('selDates').selectedIndex = 0;
				//selDates_change(document.getElementById('selDates').value);
			}
			
			function selDates_change(strValue) {
				if (document.getElementById('selDates').value.substr(0, 5) == "range") {
					document.getElementById("dateFields").style.display = "inline";
				} else {
					document.getElementById("dateFields").style.display = "none";
					if (strType == "company") {
						change_account(document.getElementById('lstCompanyName').value, 'table');
					} else if (strType == "supervisor") {
						change_supervisor(document.getElementById('lstSupervisorName').value, 'table');
					} else if (strType == "user") {
						change_statsContent('<?php echo $userId?>');
					} else {
						change_statsContent(document.getElementById('listUsers').value);
					}
				}
			}
			
			function collapse_expand(objName) {
				var obj = eval(document.getElementById(objName));
				obj.style.display = (obj.style.display=="none") ? "block" : "none";
				var objPicto = eval(document.getElementById(objName+"_picto"));
				objPicto.src = (obj.style.display=="none") ? "img/expand_2.gif" : "img/collapse_2.gif";
			}
			
			function changeLang(strLang) {
				lang = strLang;
				var objList = document.getElementById("selDates");
				var num = objList.selectedIndex;
				for(i=0; i<objList.options.length; i++) {
					objList.options.length = 0;
				}
				objList[0] = new Option(tabOptions[lang]["today"], "today");
				objList[1] = new Option(tabOptions[lang]["week"], "week");
				objList[2] = new Option(tabOptions[lang]["currentmonth"], "currentmonth");
				objList[3] = new Option(tabOptions[lang]["month"], "month");
				objList[4] = new Option(tabOptions[lang]["lastmonth"], "lastmonth");
				objList[5] = new Option(tabOptions[lang]["range"], "range");
				objList[6] = new Option(tabOptions[lang]["all"], "all");
				objList.selectedIndex = num;
				document.getElementById("lblStartDate").innerHTML = tabOptions[lang]["start"];
				document.getElementById("lblEndDate").innerHTML = tabOptions[lang]["end"];
				document.getElementById("lblUsers").innerHTML = tabOptions[lang]["users"];
				document.getElementById("lblCompanies").innerHTML = tabOptions[lang]["companies"];
				document.getElementById("lblSupervisors").innerHTML = tabOptions[lang]["supervisors"];
				if (strType == "company") {
					change_account(document.getElementById('lstCompanyName').value, 'xls');
				} else if (strType == "supervisors") {
					change_supervisor(document.getElementById('lstSupervisorName').value, 'xls');
				} else if (strType == "supervisor") {
					change_supervisor(document.getElementById('lstSupervisorName').value, 'xls');
				} else if (strType == "user") {
					change_statsContent('<?php echo $userId?>');					
				} else {
					change_statsContent(document.getElementById('listUsers').value);
				}
				var objLinkStart = document.getElementById("trigger_stats_starts");
				objLinkStart.firstChild.innerHTML = tabOptions[lang]["pick"];
				var objLinkEnd = document.getElementById("trigger_stats_ends");
				objLinkEnd.firstChild.innerHTML = tabOptions[lang]["pick"];
			}

			function search(strList) {
				var strSearch = document.getElementById("search_part").value.toLowerCase();
				var exp = new RegExp(strSearch,"g");
				var list = document.getElementById(strList);
				var intIndex = (list.selectedIndex==0)?0:list.selectedIndex +1;
				for(i=intIndex; i<list.options.length ; i++) {
					if(exp.test(list.options[i].text.toLowerCase())) {
						list.selectedIndex = i;
						break;
					}
					if(i==list.options.length -1) {
						if(confirm("Recherche terminée !\n\nReprendre au début ?")) {
							list.selectedIndex=0;
							i=0;
						}
					}
				}
			}
		</script>
		<style>
			#statsHeader {
				overflow-x			:auto;
				overflow-x			:hidden;
				background-color	:#ECE9D8;
				padding-top			:5px;
				padding-bottom		:5px;
				vertical-align		:middle;
				border-bottom		:thin black solid;
			}
			
			#title {
				float				:left;
				text-align			:center;
				font-weight			:bold;
				font-family			:Arial, "MS Sans Serif", Geneva, sans-serif;
				font-size			:medium;
			}
			
			#typedoc {
				float				:left;
				padding-left		:10px;
			}
			
			#picto_title {
				float				:right;
				width				:20px;
				padding-top			:5px;
				padding-bottom		:5px;
				vertical-align		:middle;
			}
			
			#statsOptions {
				clear				:both;
				float				:left;
				width				:100%;
				background-color	:#C5C0BB;
				border-bottom		:thin black solid;
				padding				:4px 2px 4px 2px;
			}
			
			#globalOptions {
				float				:left;
				width				:250px;
			}
			
			#dateFields {
				float				:left;
				display				:none;
				padding-right		:5px;
				clear				:both;
			}
			
			#divExport {
				float				:right;
				margin-right		:15px;
			}
			
			#statsDivWait {
				width				:100%;
				height				:60px;
				padding-top			:50px;
				text-align			:center;
			}
			
			#statsContent {
				width				:98%;
				font-family			:"MS Sans Serif", Geneva, sans-serif;
				font-size			:8pt;
				padding-left		:10px;
			}
			
			.type-date {
				width				:60px;
				font-family			:"MS Sans Serif", Geneva, sans-serif;
				font-size			:8pt;
			}
		</style>
	</head>
	<body onLoad="window.focus();">
		<div id="statsHeader">
			<div id="picto_title"><img  name="statsOptions_picto" id="statsOptions_picto" src="img/collapse_2.gif" onclick="collapse_expand('statsOptions');" /></div>
			<div id="typedoc" name="typedoc">&nbsp;
				<?php if($strType != "user" && $strType != "supervisor") {?>
					<a href="index-stats.php?lang=<?php echo $lang?>"><span id="lblUsers" name="lblUsers"><?php echo $options[$lang]["users"]; ?></span></a>
					&nbsp;<?php
					if($strType == "users") {
						echo '<img src="img/checked.gif" width="16" height="16" />';
					} else {
						echo '<img src="img/dot.gif" width="16" height="0" />';
					}?>
					<img src="img/dot.gif" width="4" height="0" />
					<a href="index-stats.php?lang=<?php echo $lang?>&company=yes"><span id="lblCompanies" name="lblCompanies"><?php echo $options[$lang]["companies"]?></span></a>
					&nbsp;<?php
					if($strType == "company") {
						echo '<img src="img/checked.gif" width="16" height="16" />';
					} else {
						echo '<img src="img/dot.gif" width="16" height="0" />';
					}?>
					<img src="img/dot.gif" width="4" height="0" />
					<a href="index-stats.php?lang=<?php echo $lang?>&supervisor=yes"><span id="lblSupervisors" name="lblSupervisors"><?php echo $options[$lang]["supervisors"]?></span></a>
					&nbsp;<?php
					if($strType == "supervisors") {
						echo '<img src="img/checked.gif" width="16" height="16" />';
					} else {
						echo '<img src="img/dot.gif" width="16" height="0" />';
					}
				} else {
					echo '';
				}?>
			</div>
			<!-- Limitation from opener -->
			<script type="text/javascript">
				if (window.opener) {
					var txt = "" + window.opener.location;
					if (txt.indexOf("index.php") >= 0) {
						document.getElementById("typedoc").style.visibility = "hidden";
					}
				}
			</script>
			<div id="title">
				<?php
				if($strType == "company") {
					$list = "lstCompanyName";
					$db = new wcmDatabase(str_replace(":3306","",$config['wcm.businessDB.connectionString']));
					$query = "select distinct companyName from biz_account where companyName Is Not Null order by companyName";
					$result = mysql_query($query);?>					
					<select id="lstCompanyName" name="lstCompanyName">
						<option value=""/>
						<option value="all"<?PHP if($company == 'all') {echo 'selected="selected"';}?>>--------------------------- ALL ---------------------------</option>
						<?php
						while($row = mysql_fetch_assoc($result)) {
							?><option value="<?php echo $row['companyName'];?>"><?php echo $row['companyName'];?></option><?php
						}?>
					</select><input type="button" value="Stats" onClick="change_account(document.getElementById('lstCompanyName').value, 'table');"/><?php
				} else if($strType == "supervisors") {
					$list = "lstSupervisorName";
					$oldid = 0;
					$oldid2 = 0;
					$oldid3 = 0;
					$oldid4 = 0;
					$oldid5 = 0;
					$db = new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));
					$query = "SELECT DISTINCT V.id,V.company,V.lName,V.fName,V.expirationDate,V2.id AS v2id,V2.company AS V2company,V2.fName AS V2fName,V2.lName AS V2lName,V2.expirationDate AS V2expDate, V3.id AS v3id,V3.company AS V3company,V3.fName AS V3fName,V3.lName AS V3lName,V3.expirationDate AS V3expDate, V4.id AS v4id,V4.company AS V4company,V4.fName AS V4fName,V4.lName AS V4lName,V4.expirationDate AS V4expDate, V5.id AS v5id,V5.company AS V5company,V5.fName AS V5fName,V5.lName AS V5lName,V5.expirationDate AS V5expDate FROM `RELAX_BIZ`.`V_USERS` V LEFT JOIN`RELAX_BIZ`.`V_USERS` V2 ON(V2.m_id=V.id AND V2.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V3 ON(V3.m_id=V2.id AND V3.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V4 ON(V4.m_id=V3.id AND V4.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V5 ON(V5.m_id=V4.id AND V5.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) WHERE V.id > 1 AND V.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR') AND V.isChiefManager = true AND V.lName NOT LIKE '%ROBOT%' ORDER BY V.lName,V.fName,V2.lName,V2.fName,V3.lName,V3.fName,V4.company,V4.lName,V4.fName,V5.lName,V5.fName"; 
					$result = mysql_query($query);?>					
					<select id="lstSupervisorName" name="lstSupervisorName">
						<option value=""/>
						<option value="0"<?PHP if($supervisor == 'all') {echo 'selected="selected"';}?>>--------------------------- ALL ---------------------------</option>
						<?php
						while($row = mysql_fetch_assoc($result)) {
							if($row['id'] != $oldid) {
								if(is_null($row['expirationDate']) || $row['expirationDate'] >= date("Y-d-m")) {
									?><option value="<?php echo $row['id'];?>" title="ID = <?php echo $row['id'];?>"><?php echo $row['lName']." ".$row['fName'];?><?php if(!is_null($row['expirationDate']) && $row['expirationDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid = $row['id'];
							}
							if($row['v2id'] != $oldid2) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v2id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V2expDate']) || $row['V2expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v2id'];?>" title="ID = <?php echo $row['v2id'];?>">.....<?php echo $row['V2lName']." ".$row['V2fName'];?><?php if(!is_null($row['V2expDate']) && $row['V2expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid2 = $row['v2id'];
							} elseif($row['v3id'] != $oldid3) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v3id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V3expDate']) || $row['V3expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v3id'];?>" title="ID = <?php echo $row['v3id'];?>">.....-----<?php echo $row['V3lName']." ".$row['V3fName'];?><?php if(!is_null($row['V3expDate']) && $row['V3expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid3 = $row['v3id'];
							} elseif($row['v4id'] != $oldid4) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v4id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V4expDate']) || $row['V4expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v4id'];?>" title="ID = <?php echo $row['v4id'];?>">.....-----.....<?php echo $row['V4lName']." ".$row['V4fName'];?><?php if(!is_null($row['V4expDate']) && $row['V4expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid4 = $row['v4id'];
							} elseif($row['v5id'] != $oldid5) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v5id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V5expDate']) || $row['V5expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v5id'];?>" title="ID = <?php echo $row['v5id'];?>">.....-----.....___<?php echo $row['V5lName']." ".$row['V5fName'];?><?php if(!is_null($row['V5expDate']) && $row['V5expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid5 = $row['v5id'];
							}
						}?>
					</select><input type="button" value="Stats" onClick="change_supervisor(document.getElementById('lstSupervisorName').value, 'table');"/><?php
				} else if($strType == "supervisor") {
					$list = "lstSupervisorName";
					$oldid = 0;
					$oldid2 = 0;
					$oldid3 = 0;
					$oldid4 = 0;
					$oldid5 = 0;
					$db = new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));
					$query = "SELECT DISTINCT V.id,V.company,V.lName,V.fName,V.expirationDate,V2.id AS v2id,V2.company AS V2company,V2.fName AS V2fName,V2.lName AS V2lName,V2.expirationDate AS V2expDate, V3.id AS v3id,V3.company AS V3company,V3.fName AS V3fName,V3.lName AS V3lName,V3.expirationDate AS V3expDate, V4.id AS v4id,V4.company AS V4company,V4.fName AS V4fName,V4.lName AS V4lName,V4.expirationDate AS V4expDate, V5.id AS v5id,V5.company AS V5company,V5.fName AS V5fName,V5.lName AS V5lName,V5.expirationDate AS V5expDate FROM `RELAX_BIZ`.`V_USERS` V LEFT JOIN`RELAX_BIZ`.`V_USERS` V2 ON(V2.m_id=V.id AND V2.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V3 ON(V3.m_id=V2.id AND V3.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V4 ON(V4.m_id=V3.id AND V4.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) LEFT JOIN`RELAX_BIZ`.`V_USERS` V5 ON(V5.m_id=V4.id AND V5.profile IN ('_ACCOUNT_PROFILE_SUPERVISOR','_ACCOUNT_PROFILE_MANAGER')) WHERE V.id = ".$managerId." ORDER BY V.lName,V.fName,V2.lName,V2.fName,V3.lName,V3.fName,V4.company,V4.lName,V4.fName,V5.lName,V5.fName"; 
					$result = mysql_query($query);?>					
					<select id="lstSupervisorName" name="lstSupervisorName">
						<?php
						while($row = mysql_fetch_assoc($result)) {
							if($row['id'] != $oldid) {
								if(is_null($row['expirationDate']) || $row['expirationDate'] >= date("Y-d-m")) {
									?><option value="<?php echo $row['id'];?>" title="ID = <?php echo $row['id'];?>"><?php echo $row['lName']." ".$row['fName'];?><?php if(!is_null($row['expirationDate']) && $row['expirationDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid = $row['id'];
							}
							if($row['v2id'] != $oldid2) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v2id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V2expDate']) || $row['V2expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v2id'];?>" title="ID = <?php echo $row['v2id'];?>">.....<?php echo $row['V2lName']." ".$row['V2fName'];?><?php if(!is_null($row['V2expDate']) && $row['V2expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid2 = $row['v2id'];
							} elseif($row['v3id'] != $oldid3) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v3id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V3expDate']) || $row['V3expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v3id'];?>" title="ID = <?php echo $row['v3id'];?>">.....-----<?php echo $row['V3lName']." ".$row['V3fName'];?><?php if(!is_null($row['V3expDate']) && $row['V3expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid3 = $row['v3id'];
							} elseif($row['v4id'] != $oldid4) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v4id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V4expDate']) || $row['V4expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v4id'];?>" title="ID = <?php echo $row['v4id'];?>">.....-----.....<?php echo $row['V4lName']." ".$row['V4fName'];?><?php if(!is_null($row['V4expDate']) && $row['V4expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid4 = $row['v4id'];
							} elseif($row['v5id'] != $oldid5) {
								$query = "SELECT count(DISTINCT wcmUserId) FROM `RELAX_BIZ`.`biz_account` WHERE managerId = ".$row['v5id'];
								$account = mysql_query($query);
								$resultat=mysql_fetch_row($account);
								if($resultat[0] > 0 && (is_null($row['V5expDate']) || $row['V5expDate'] >= date("Y-d-m"))) {
									?><option value="<?php echo $row['v5id'];?>" title="ID = <?php echo $row['v5id'];?>">.....-----.....___<?php echo $row['V5lName']." ".$row['V5fName'];?><?php if(!is_null($row['V5expDate']) && $row['V5expDate'] < date("Y-d-m")) echo " (x)";?></option><?php
								}
								$oldid5 = $row['v5id'];
							}
						}?>
					</select>
					<input type="button" value="Stats" onClick="change_supervisor(document.getElementById('lstSupervisorName').value, 'table');"/><?php
				} else if($strType == "users") {
					$list = "listUsers";
					$db = new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));
					$query = "select id, concat(UPPER(right(name,CHAR_LENGTH(Name)-LOCATE('|',Name))), ' ', left(name, LOCATE('|',Name)-1)) as name from wcm_user where isAdministrator=0 order by concat(UPPER(right(name,CHAR_LENGTH(Name)-LOCATE('|',Name))), ' ', left(name, LOCATE('|',Name)-1))";
					$result = mysql_query($query);?>
					<select id="listUsers" name="listUsers" />
						<option value="0"/><?php					
						while($row = mysql_fetch_assoc($result)) {
							?><option value="<?php echo $row['id'];?>"><?php echo $row['name'] . ' (' . $row['id'] . ')';?></option><?php
						}?>
					</select><input type="button" value="Stats" onClick="reset_SelDates(); change_statsContent(document.getElementById('listUsers').value);"/><?php
				} else {
					$db = new wcmDatabase(str_replace(":3306","",$config['wcm.systemDB.connectionString']));
					$query = "select concat(UPPER(right(name,CHAR_LENGTH(Name)-LOCATE('|',Name))), ' ', left(name, LOCATE('|',Name)-1)) as name from wcm_user where id = '" . $userId . "'";
					$result = mysql_query($query);
					while($row = mysql_fetch_assoc($result)) {
						$userName	= $row['name'];
					}
					echo $userName . " (ID: " . $userId . ")";
				}
				if($strType != "user") {?>
					&nbsp;<input type="text" id="search_part" name="search_part" style="width:150px" />
					&nbsp;<input type="button" value="Chercher" onClick="search('<?php echo $list;?>')" />
				<?php }?>
			</div>
			<div name="statsOptions" id="statsOptions">
				<div name="globalOptions" id="globalOptions">
					<input type="radio" id="lang_en" name="lang" value="en" <?php if($lang=='en') echo ' checked="true"';?> onClick="changeLang(this.value);" /><label for="lang_en">EN</label>
					<img src="img/dot.gif" width="5" height="0" />
					<input type="radio" id="lang_fr" name="lang" value="fr" <?php if($lang=='fr') echo ' checked="true"';?> onClick="changeLang(this.value);" /><label for="lang_fr">FR</label>
					<img src="img/dot.gif" width="30" height="0" />
					<?php if($strType == "company") {?>
						<select name="selDates" id="selDates" onchange="selDates_change(document.getElementById('lstCompanyName').value);"><?php
					} else if($strType == "supervisor"){?>
						<select name="selDates" id="selDates" onchange="selDates_change(document.getElementById('lstSupervisorName').value);"><?php
					} else if($strType == "user"){?>
						<select name="selDates" id="selDates" onchange="selDates_change('<?php echo $userId?>');"><?php
					} else {?>
						<select name="selDates" id="selDates" onchange="selDates_change(document.getElementById('listUsers').value);"><?php
					}?>
						<option value="today"><?php echo $options[$lang]["today"]?></option>
						<option value="week"><?php echo $options[$lang]["week"]?></option>
						<option value="currentmonth"><?php echo $options[$lang]["currentmonth"]?></option>
						<option value="month" selected><?php echo $options[$lang]["month"]?></option>
						<option value="lastmonth"><?php echo $options[$lang]["lastmonth"]?></option>
						<option value="range"><?php echo $options[$lang]["range"]?></option>
						<option value="all"><?php echo $options[$lang]["all"]?></option>
					</select>
				</div>
				<div name="dateFields" id="dateFields">
					<img src="img/dot.gif" width="4" height="0" />
					<label id="lblStartDate" name="lblStartDate" class="label_date"><?php echo $options[$lang]["start"]?></label>
					<?php $calendar = new wcmHtmlCalendar();
			        echo $calendar->render('stats_starts', date('Y-m-d'), 'date', null, false, $lang);?>
					<img src="img/dot.gif" width="4" height="0" />
					<label id="lblEndDate" name="lblEndDate" class="label_date"><?php echo $options[$lang]["end"]?></label>
					<?php $calendar = new wcmHtmlCalendar();
			        echo $calendar->render('stats_ends', date('Y-m-d'), 'date', null, false, $lang);?>
					<img src="img/dot.gif" width="4" height="0" />
					<?php if($strType == "company") {?>
						<input type="image" src="img/actions/recherche.gif" width="16" height="16" onclick="change_account(document.getElementById('lstCompanyName').value, 'table');" /><?php
					} else if($strType == "supervisor" || $strType == "supervisors"){?>
						<input type="image" src="img/actions/recherche.gif" width="16" height="16" onclick="change_supervisor(document.getElementById('lstSupervisorName').value, 'table');" /><?php
					} else if($strType == "user"){?>
						<input type="image" src="img/actions/recherche.gif" width="16" height="16" onclick="change_statsContent('<?php echo $userId?>');" /><?php
					} else {?>
						<input type="image" src="img/actions/recherche.gif" width="16" height="16" onclick="change_statsContent(document.getElementById('listUsers').value);" /><?php
					}?>
				</div>
				<div name="divExport" id="divExport">
					<?php if($strType == "company") {?>
						<input type="button" value="Export" onClick="change_account(document.getElementById('lstCompanyName').value, 'xls');"/><?php
					} else if($strType == "supervisor"){?>
						<input type="button" value="Export" onClick="change_supervisor(document.getElementById('lstSupervisorName').value, 'xls');"/><?php
					}?>
				</div>
			</div>
		</div>
		<div name="statsContent" id="statsContent" />
		<?php if($userId != ""){?>
			<script type="text/javascript">change_statsContent("<?php echo $userId?>");</script><?php
		 } ?>
	</body>
</html>

