<?php
$temps_debut = microtime(true);

/**
 * @todo Listids + Channelids mal serializé ! génère des warnings et notice :s 
 */
require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');


set_time_limit(0);
$date = date("Y-m-d");

$project = wcmProject::getInstance();
$session = wcmSession::getInstance();
//Sert pour l'accès au webservice texml
$config = wcmConfig::getInstance();
//Instanciation de texml
$search = wcmBizsearch::getInstance($config['wcm.search.engine']);
//Génération d'un id unique pour la requete (cache)
$uid = 'relaxTask_'.(isset($_POST["period"])?$_POST["period"]:"0").'_'.uniqid();
$siteId = $session->getSiteId();
echo '<fieldset><form action="index.php?_wcmAction=business/statistics" method="post">';

/*if(isset($_POST['beginDate']))
	wcmGUI::renderDateField('beginDate', $_POST['beginDate'], _BIZ_BEGINPERIODDATE, 'date');
else*/
if(isset($_POST['beginDate']))
	wcmGUI::renderDateField('beginDate', $_POST['beginDate'], _BIZ_BEGINPERIODDATE, 'date');
else 
	wcmGUI::renderDateField('beginDate', date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y"))), _BIZ_BEGINPERIODDATE, 'date');
if(isset($_POST['endDate']))
	wcmGUI::renderDateField('endDate', $_POST['endDate'], _BIZ_END_DATE, 'date');
else
	wcmGUI::renderDateField('endDate', date("Y-m-d", mktime(23,59,59,date("m"),date("d")-1,date("Y"))), _BIZ_BEGINPERIODDATE, 'date');

$arraytest = array(0=>_LAST_DAY,1=>_LAST_WEEK,2=>_LAST_MONTH,3=>_LAST_YEAR,4=>_CUSTOM);
wcmGUI::renderDropdownField('period', $arraytest, (isset($_POST["period"])?$_POST["period"]:"4"), _PERIOD. " : ");
//wcmFormGUI::renderBooleanField("test",false,"ceci est une case a cocher de test");
echo '<input type="submit" value="Valider">';
echo '</form></fieldset>';
//echo isset($_POST["_wcmBoxtest"])?"coché":"pascoché";

if(isset($_POST["period"])){
	switch($_POST["period"]){
		//LAST_DAY
		case 0:
			$date_debut = date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			$date_fin = date("Y-m-d H:i:s", mktime(23,59,59,date("m"),date("d")-1,date("Y")));
			break;
		//LAST_WEEK
		case 1:
			$date_debut = date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d")-7,date("Y")));
			$date_fin = date("Y-m-d H:i:s", mktime(23,59,59,date("m"),date("d")-1,date("Y")));
			break;
		//LAST_MONTH
		case 2:
			$date_debut = date("Y-m-d H:i:s", mktime(0,0,0,date("m")-1,date("d"),date("Y")));
			$date_fin = date("Y-m-d H:i:s", mktime(23,59,59,date("m"),date("d")-1,date("Y")));
			break;
		//LAST_YEAR
		case 3:
			$date_debut = date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d"),date("Y")-1));
			$date_fin = date("Y-m-d H:i:s", mktime(23,59,59,date("m"),date("d")-1,date("Y")));
			break;
		//CUSTOM
		case 4:
			$date_debut = $_POST['beginDate']." 00:00:00";
			$date_fin = $_POST['endDate']." 23:59:59";
			break;
		default:
			break;
	}
}else{
	
	$date_debut = date("Y-m-d H:i:s", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
	$date_fin = date("Y-m-d H:i:s", mktime(23,59,59,date("m"),date("d")-1,date("Y")));
			
}

//Initialisation d'une connexion à la bdd
$db_biz = wcmProject::getInstance()->bizlogic->getBizClassByClassName('channel')->getConnector()->getBusinessDatabase();

$countChannelIds = array();

$site_array = array(4=>"AFP/EN",5=>"AFP/FR",6=>"Relaxfil");
$object_array = array(0 => "news",1 => "slideshow",2 => "video");
$source_array = array(10 =>"AFP" ,12=>"Relaxnews");
echo "<center><h1>Totaux par univers <b>(BETA)</b></h1></center>";
echo "<table class='tabCat'>";
echo "<tr><th></th><th colspan='2'><b>News</th><th colspan='2'>Slideshow</th><th colspan='2'>Video</b></th></tr>";
echo "<tr><th></th><th>AFP</th><th>Relaxnews</th><th>AFP</th><th>Relaxnews</th><th>AFP</th><th>Relaxnews</th></tr>";
foreach($site_array as $i=>$site){
	echo "<tr>";	
	echo "<td>".$site_array[$i]."</td>";
	foreach($object_array as $object){
		foreach($source_array as $id_source=>$source){
			$query = "SELECT s.id AS siteId,n.source, COUNT( n.id ) AS total
			FROM biz_".$object." n
			LEFT JOIN biz_site s ON ( s.id = n.siteId )
			WHERE n.publicationdate >= '".$date_debut."'
			AND n.publicationdate <= '".$date_fin."'
			AND n.source = ".$id_source."
			AND n.Siteid = ".$i."
			AND n.workflowstate = 'published'
			GROUP BY siteId";
			//echo $query."<br />";
			$rsChannelId = $db_biz->executeQuery($query);
			$rsChannelId->first();
			if(!$rsChannelId->getRow()){
				echo "<td>0</td>";
			}else{			
				while ($record = $rsChannelId->getRow()) {
					@$super_total[$record["id"]][$object][$record["source"]] = $record["total"]; 			
					echo "<td>".$record["total"]."</td>";
					$continue = @$rsChannelId->next();
				}
			}
			$rsChannelId->close();
		}
	}
	echo "</tr>";
}
echo "</table>";

$query = "SELECT n.channelId,n.channelIds,n.listIds,n.source
FROM biz_news n
WHERE n.publicationdate >= '".$date_debut."'
AND n.publicationdate <= '".$date_fin."'
AND n.siteId = ".$siteId."
AND n.source IS NOT NULL";

//Initialisation du tableau de comptage
$countChannel = array();
$rsChannelId = $db_biz->executeQuery($query);
$rsChannelId->first();

while ($record = $rsChannelId->getRow()) {
	//On compte les channelId (en fonction de la source)
	if(isset($countChannel["id"][$record["channelId"]][$record["source"]])){
		$countChannel["id"][$record["channelId"]][$record["source"]] = $countChannel["id"][$record["channelId"]][$record["source"]] + 1;			
	}else{
		$countChannel["id"][$record["channelId"]][$record["source"]] = 1;
	}
	//On compte ensuite les channelIds (en fonction de la source)
	if(!empty($record["channelIds"])){
		$channelIdsArray = @unserialize($record["channelIds"]);	
		if(is_array($channelIdsArray)){	
			foreach($channelIdsArray as $channelIds){
				if($channelIds != NULL){
					if (!empty($countChannel["ids"][$channelIds][$record["source"]]))					
						$countChannel["ids"][$channelIds][$record["source"]] = $countChannel["ids"][$channelIds][$record["source"]] + 1;
					else						
						$countChannel["ids"][$channelIds][$record["source"]] = 1;					
				}
			}
		}
	}
	//Puis les listIds (en fonction de la source)
	if(!empty($record["listIds"])){
		$listIdsArray = unserialize($record["listIds"]);
		foreach($listIdsArray as $listIds){
			if($listIds != NULL){
				if(isset($countChannel["lids"][$listIds]))
					$countChannel["lids"][$listIds] = $countChannel["lids"][$listIds] + 1;
				else
					$countChannel["lids"][$listIds] = 1;
			}
		}
	}
	$continue = @$rsChannelId->next();
}
$rsChannelId->close();

//Même chose pour les slideshows
$query = "SELECT s.channelId,s.channelIds,s.listIds,s.source
FROM biz_slideshow s
WHERE s.publicationdate >= '".$date_debut."'
AND s.publicationdate <= '".$date_fin."'
AND s.siteId = ".$siteId."
AND s.source IS NOT NULL";
//Initialisation du tableau de comptage
$countSlideshow = array();
$rsChannelId = $db_biz->executeQuery($query);
$rsChannelId->first();
while ($record = $rsChannelId->getRow()) {
	//On compte les channelId (en fonction de la source)
	if(isset($countSlideshow["id"][$record["channelId"]][$record["source"]]))
		$countSlideshow["id"][$record["channelId"]][$record["source"]] = $countSlideshow["id"][$record["channelId"]][$record["source"]] + 1;
	else
		$countSlideshow["id"][$record["channelId"]][$record["source"]] = 1;
	//On compte ensuite les channelIds (en fonction de la source)
	if(!empty($record["channelIds"])){
		$channelIdsArray = unserialize($record["channelIds"]);
		foreach($channelIdsArray as $channelIds){
			if($channelIds != NULL){
				if(isset($countSlideshow["ids"][$channelIds][$record["source"]]))
					$countSlideshow["ids"][$channelIds][$record["source"]] = $countSlideshow["ids"][$channelIds][$record["source"]] + 1;
				else
					$countSlideshow["ids"][$channelIds][$record["source"]] = 1;
			}
		}
	}
	//Puis les listIds (en fonction de la source)
	if(!empty($record["listIds"])){
		$listIdsArray = unserialize($record["listIds"]);
		foreach($listIdsArray as $listIds){
			if($listIds != NULL){
				if(isset($countSlideshow["lids"][$listIds][$record["source"]]))
					$countSlideshow["lids"][$listIds][$record["source"]] = $countSlideshow["lids"][$listIds][$record["source"]] + 1;
				else
					$countSlideshow["lids"][$listIds][$record["source"]] = 1;
			}
		}
	}
	$continue = @$rsChannelId->next();
}
$rsChannelId->close();

//Même chose pour les videos
$query = "SELECT v.channelId,v.channelIds,v.listIds,v.source
FROM biz_video v
WHERE v.publicationdate >= '".$date_debut."'
AND v.publicationdate <= '".$date_fin."'
AND v.siteId = ".$siteId."
AND v.source IS NOT NULL";
//Initialisation du tableau de comptage
$countVideo = array();
$rsChannelId = $db_biz->executeQuery($query);
$rsChannelId->first();

while ($record = $rsChannelId->getRow()) {
	//On compte les channelId (en fonction de la source)
	if(isset($countVideo["id"][$record["channelId"]][$record["source"]]))
		$countVideo["id"][$record["channelId"]][$record["source"]] = $countVideo["id"][$record["channelId"]][$record["source"]] + 1;
	else
		$countVideo["id"][$record["channelId"]][$record["source"]] = 1;

	//On compte ensuite les channelIds (en fonction de la source)
	if(!empty($record["channelIds"])){
		$channelIdsArray = unserialize($record["channelIds"]);
		foreach($channelIdsArray as $channelIds){
			if($channelIds != NULL){
				if(isset($countVideo["ids"][$channelIds][$record["source"]]))
					$countVideo["ids"][$channelIds][$record["source"]] = $countVideo["ids"][$channelIds][$record["source"]] + 1;
				else
					$countVideo["ids"][$channelIds][$record["source"]] = 1;
			}
		}
	}
	//Puis les listIds (en fonction de la source)
	if(!empty($record["listIds"])){
		$listIdsArray = unserialize($record["listIds"]);
		foreach($listIdsArray as $listIds){
			if($listIds != NULL){
				if(isset($countVideo["lids"][$listIds][$record["source"]]))
					$countVideo["lids"][$listIds][$record["source"]] = $countVideo["lids"][$listIds][$record["source"]] + 1;
				else
					$countVideo["lids"][$listIds][$record["source"]] = 1;
			}
		}
	}
	$continue = @$rsChannelId->next();
}
$rsChannelId->close();
echo "<br />";
$channel = new channel();
$tot_news_be[12]=$tot_news_be[10]=$tot_slideshow_be[12]=$tot_slideshow_be[10]=$tot_video_be[12]=$tot_video_be[10]=$tot_news_ma[12]=$tot_news_ma[10]=$tot_slideshow_ma[12]=$tot_slideshow_ma[10]=$tot_video_ma[12]=$tot_video_ma[10]=$tot_news_di[12]=$tot_news_di[10]=$tot_slideshow_di[12]=$tot_slideshow_di[10]=$tot_video_di[12]=$tot_video_di[10]=$tot_news_to[12]=$tot_news_to[10]=$tot_slideshow_to[12]=$tot_slideshow_to[10]=$tot_video_to[12]=$tot_video_to[10] = 0;

foreach($channel->getChannelHierarchy() as $id=>$channel){
	$class_css = new channel(null,$id);
	$subchannel = explode(" :: ",$channel);
	switch($subchannel[0]){
		case "Bien-être":
			$tot_news_be[12] = $tot_news_be[12] + @$countChannel["id"][$id][12];
			$tot_news_be[10] = $tot_news_be[10] + @$countChannel["id"][$id][10];
			$tot_slideshow_be[12] = $tot_slideshow_be[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_be[10] = $tot_slideshow_be[10] + @$countSlideshow["id"][$id][10];
			$tot_video_be[12] = $tot_video_be[12] + @$countVideo["id"][$id][12];
			$tot_video_be[10] = $tot_video_be[10] + @$countVideo["id"][$id][10];			
		break;	
		case "Well-being":
			$tot_news_be[12] = $tot_news_be[12] + @$countChannel["id"][$id][12];
			$tot_news_be[10] = $tot_news_be[10] + @$countChannel["id"][$id][10];
			$tot_slideshow_be[12] = $tot_slideshow_be[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_be[10] = $tot_slideshow_be[10] + @$countSlideshow["id"][$id][10];
			$tot_video_be[12] = $tot_video_be[12] + @$countVideo["id"][$id][12];
			$tot_video_be[10] = $tot_video_be[10] + @$countVideo["id"][$id][10];			
		break;
		case "Maison":
			//Total news
			$tot_news_ma[12] = $tot_news_ma[12] + @$countChannel["id"][$id][12];
			$tot_news_ma[10] = $tot_news_ma[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_ma[12] = $tot_slideshow_ma[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_ma[10] = $tot_slideshow_ma[10] + @$countSlideshow["id"][$id][10];
			//Total video			
			$tot_video_ma[12] = $tot_video_ma[12] + @$countVideo["id"][$id][12];
			$tot_video_ma[10] = $tot_video_ma[10] + @$countVideo["id"][$id][10];
		break;	
		case "House & Home":
			//Total news
			$tot_news_ma[12] = $tot_news_ma[12] + @$countChannel["id"][$id][12];
			$tot_news_ma[10] = $tot_news_ma[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_ma[12] = $tot_slideshow_ma[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_ma[10] = $tot_slideshow_ma[10] + @$countSlideshow["id"][$id][10];
			//Total video			
			$tot_video_ma[12] = $tot_video_ma[12] + @$countVideo["id"][$id][12];
			$tot_video_ma[10] = $tot_video_ma[10] + @$countVideo["id"][$id][10];
		break;	
		case "Divertissement":
			//Total news
			$tot_news_di[12] = $tot_news_di[12] + @$countChannel["id"][$id][12];
			$tot_news_di[10] = $tot_news_di[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_di[12] = $tot_slideshow_di[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_di[10] = $tot_slideshow_di[10] + @$countSlideshow["id"][$id][10];
			//Total video
			$tot_video_di[12] = $tot_video_di[12] + @$countVideo["id"][$id][12];
			$tot_video_di[10] = $tot_video_di[10] + @$countVideo["id"][$id][10];
		break;	
		case "Entertainment":
			//Total news
			$tot_news_di[12] = $tot_news_di[12] + @$countChannel["id"][$id][12];
			$tot_news_di[10] = $tot_news_di[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_di[12] = $tot_slideshow_di[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_di[10] = $tot_slideshow_di[10] + @$countSlideshow["id"][$id][10];
			//Total video
			$tot_video_di[12] = $tot_video_di[12] + @$countVideo["id"][$id][12];
			$tot_video_di[10] = $tot_video_di[10] + @$countVideo["id"][$id][10];
		break;
		case "Tourisme":
			//Total news
			$tot_news_to[12] = $tot_news_to[12] + @$countChannel["id"][$id][12];
			$tot_news_to[10] = $tot_news_to[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_to[12] = $tot_slideshow_to[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_to[10] = $tot_slideshow_to[10] + @$countSlideshow["id"][$id][10];
			//Total video
			$tot_video_to[12] = $tot_video_to[12] + @$countVideo["id"][$id][12];
			$tot_video_to[10] = $tot_video_to[10] + @$countVideo["id"][$id][10];
		break;
		case "Tourism":
			//Total news
			$tot_news_to[12] = $tot_news_to[12] + @$countChannel["id"][$id][12];
			$tot_news_to[10] = $tot_news_to[10] + @$countChannel["id"][$id][10];
			//Total slideshow
			$tot_slideshow_to[12] = $tot_slideshow_to[12] + @$countSlideshow["id"][$id][12];
			$tot_slideshow_to[10] = $tot_slideshow_to[10] + @$countSlideshow["id"][$id][10];
			//Total video
			$tot_video_to[12] = $tot_video_to[12] + @$countVideo["id"][$id][12];
			$tot_video_to[10] = $tot_video_to[10] + @$countVideo["id"][$id][10];
		break;	
		
	}
}

if(!empty($countChannel)){
	$channel = new channel();
	echo "<center><h1>Eléments publiés par cat&eacute;gorie<br />entre ".$date_debut." et ".$date_fin."</h1></center>";
	echo "<table class='tabCat'><tbody>";
	if($siteId == 5 || $siteId == 4)
		echo "<tr><th></th><th colspan='2'><b>News</b></th><th colspan='2'><b>Slideshow</b></th><th colspan='2'><b>Video</b></th></tr>";
	else
		echo "<tr><th></th><th><b>News</b></th><th><b>Slideshow</b></th><th><b>Video</b></th></tr>";	
	if($siteId == 5 || $siteId == 4)
		echo "<tr><th></th><th><b>AFP</b></th><th><b>Relaxnews</b></th><th><b>AFP</b></th><th><b>Relaxnews</b></th><th><b>AFP</b></th><th><b>Relaxnews</b></th></tr>";
	
	foreach($channel->getChannelHierarchy() as $id=>$channel){
		//On crée un objet channel pour récupérer le champ css (affichage de la couleur)
		$class_css = new channel(null,$id);
		$subchannel = explode(" :: ",$channel);		
		echo "<tr>";		
		
		//Si c'est la catégorie "pilier"
		if(count($subchannel) == 1 && $subchannel[0] != ""){
			
			switch($subchannel[0]){
				case "Bien-être":
					$total_news[12] = $tot_news_be[12];
					$total_news[10] = $tot_news_be[10];
					$total_slideshow[12] = $tot_slideshow_be[12];
					$total_slideshow[10] = $tot_slideshow_be[10];
					$total_video[12] = $tot_video_be[12];
					$total_video[10] = $tot_video_be[10];

				break;	
				case "Well-being":
					$total_news[12] = $tot_news_be[12];
					$total_news[10] = $tot_news_be[10];
					$total_slideshow[12] = $tot_slideshow_be[12];
					$total_slideshow[10] = $tot_slideshow_be[10];
					$total_video[12] = $tot_video_be[12];
					$total_video[10] = $tot_video_be[10];

				break;
				case "Maison":
					$total_news[12] = $tot_news_ma[12];
					$total_news[10] = $tot_news_ma[10];
					$total_slideshow[12] = $tot_slideshow_ma[12];
					$total_slideshow[10] = $tot_slideshow_ma[10];
					$total_video[12] = $tot_video_ma[12];
					$total_video[10] = $tot_video_ma[10];
				break;	
				case "House & Home":
					$total_news[12] = $tot_news_ma[12];
					$total_news[10] = $tot_news_ma[10];
					$total_slideshow[12] = $tot_slideshow_ma[12];
					$total_slideshow[10] = $tot_slideshow_ma[10];
					$total_video[12] = $tot_video_ma[12];
					$total_video[10] = $tot_video_ma[10];
				break;
				case "Divertissement":
					$total_news[12] = $tot_news_di[12];
					$total_news[10] = $tot_news_di[10];
					$total_slideshow[12] = $tot_slideshow_di[12];
					$total_slideshow[10] = $tot_slideshow_di[10];
					$total_video[12] = $tot_video_di[12];
					$total_video[10] = $tot_video_di[10];
				break;	
				case "Entertainment":
					$total_news[12] = $tot_news_di[12];
					$total_news[10] = $tot_news_di[10];
					$total_slideshow[12] = $tot_slideshow_di[12];
					$total_slideshow[10] = $tot_slideshow_di[10];
					$total_video[12] = $tot_video_di[12];
					$total_video[10] = $tot_video_di[10];
				break;
				case "Tourisme":
					$total_news[12] = $tot_news_to[12];
					$total_news[10] = $tot_news_to[10];
					$total_slideshow[12] = $tot_slideshow_to[12];
					$total_slideshow[10] = $tot_slideshow_to[10];
					$total_video[12] = $tot_video_to[12];
					$total_video[10] = $tot_video_to[10];
				break;
				case "Tourism":
					$total_news[12] = $tot_news_to[12];
					$total_news[10] = $tot_news_to[10];
					$total_slideshow[12] = $tot_slideshow_to[12];
					$total_slideshow[10] = $tot_slideshow_to[10];
					$total_video[12] = $tot_video_to[12];
					$total_video[10] = $tot_video_to[10];
				break;	
			}

			//Nom de la catégorie, on met en gras si catégorie composite
			echo "<td class='".$class_css->css."'><b>".$subchannel[0]."</b></td>";		
			//Nb en tant que catégorie primaire source AFP
			if($siteId == 5 || $siteId == 4)
				echo "<td class='".$class_css->css."'><b>".$total_news[10]/*(isset($countChannel["id"][$id][10])?$countChannel["id"][$id][10]:0)*/." (".(isset($countChannel["ids"][$id][10])?$countChannel["ids"][$id][10]:"0").")</b></td>";
			//Nb en tant que catégorie primaire source relaxnews
			echo "<td class='".$class_css->css."'><b>".$total_news[12]/*(isset($countChannel["id"][$id][12])?$countChannel["id"][$id][12]:0)*/." (".(isset($countChannel["ids"][$id][12])?$countChannel["ids"][$id][12]:"0").")</b></td>";
			//Nb de slideshow en catégorie primaire source AFP
			if($siteId == 5 || $siteId == 4)			
				echo "<td class='".$class_css->css."'><b>".$total_slideshow[10]/*(isset($countSlideshow["id"][$id][10])?$countSlideshow["id"][$id][10]:0)*/." (".(isset($countSlideshow["ids"][$id][10])?$countSlideshow["ids"][$id][10]:"0").")</b></td>";
			//Nb de slideshow en catégorie primaire source relaxnews
			echo "<td class='".$class_css->css."'><b>".$total_slideshow[12]/*(isset($countSlideshow["id"][$id][12])?$countSlideshow["id"][$id][12]:0)*/." (".(isset($countSlideshow["ids"][$id][12])?$countSlideshow["ids"][$id][12]:"0").")</b></td>";
			//Nb de video en catégorie primaire source AFP
			if($siteId == 5 || $siteId == 4)			
				echo "<td class='".$class_css->css."'><b>".$total_video[10]/*(isset($countVideo["id"][$id][10])?$countVideo["id"][$id][10]:0)*/." (".(isset($countVideo["ids"][$id][10])?$countVideo["ids"][$id][10]:"0").")</b></td>";
			//Nb de video en catégorie primaire source relaxnews
			echo "<td class='".$class_css->css."'><b>".$total_video[12]/*(isset($countVideo["id"][$id][12])?$countVideo["id"][$id][12]:0)*/." (".(isset($countVideo["ids"][$id][12])?$countVideo["ids"][$id][12]:"0").")</b></td>";
		}elseif($subchannel[0] != ""){			
			//Nom de la catégorie
			if(strpos($subchannel[count($subchannel)-1],"-") !== false && $subchannel[count($subchannel)-1] != "High-Tech")
				echo "<td><b>".$subchannel[count($subchannel)-1]."</b></td>";
			else
				echo "<td>".$subchannel[count($subchannel)-1]."</td>";
			
			//Nb en tant que catégorie primaire source AFP 
			if($siteId == 5 || $siteId == 4)			
				echo "<td>".(isset($countChannel["id"][$id][10])?$countChannel["id"][$id][10]:"0")." (".(isset($countChannel["ids"][$id][10])?$countChannel["ids"][$id][10]:"0").")</td>";
			//Nb en tant que catégorie primaire source Relaxnews
			echo "<td>".(isset($countChannel["id"][$id][12])?$countChannel["id"][$id][12]:"0")." (".(isset($countChannel["ids"][$id][12])?$countChannel["ids"][$id][12]:"0").")</td>";
			//Nb de slideshow en catégorie primaire source AFP
			if($siteId == 5 || $siteId == 4)			
				echo "<td>".(isset($countSlideshow["id"][$id][10])?$countSlideshow["id"][$id][10]:"0")." (".(isset($countSlideshow["ids"][$id][10])?$countSlideshow["ids"][$id][10]:"0").")</td>";
			//Nb en tant que catégorie primaire source Relaxnews
			echo "<td>".(isset($countSlideshow["id"][$id][12])?$countSlideshow["id"][$id][12]:"0")." (".(isset($countSlideshow["ids"][$id][12])?$countSlideshow["ids"][$id][12]:"0").")</td>";
			//Nb de video en catégorie primaire source AFP
			if($siteId == 5 || $siteId == 4)			
				echo "<td>".(isset($countVideo["id"][$id][10])?$countVideo["id"][$id][10]:"0")." (".(isset($countVideo["ids"][$id][10])?$countVideo["ids"][$id][10]:"0").")</td>";
			//Nb de video en catégorie primaire source Relaxnews
			echo "<td>".(isset($countVideo["id"][$id][12])?$countVideo["id"][$id][12]:"0")." (".(isset($countVideo["ids"][$id][12])?$countVideo["ids"][$id][12]:"0").")</td>";
		}
		echo "</tr>";
	}
	echo "</tbody></table>";
	//LISTIDS
	echo "<center><h1>Eléments publiés par Tag<br />entre ".$date_debut." et ".$date_fin."</h1></center>";
	$list = new wcmList();
	$listArray = $list->getArborescenceList();
	$lang = $session->getSite()->language;
	//on recupère les listids des News
	$listsIdsAllowed = getRootsItemFromXml("news", 'rootLists', 'list');
	foreach($listsIdsAllowed as $id=>$listsIds){
		$list = new wcmList(); 
		$realId = $list->getIdFromCode($listsIds);
		$supremArray[] = $list->getContent($realId);
	}
	echo "<table width='100%' align='center' class='bigTabTag'>";
	echo "<tr>";	
	foreach($supremArray as $sar){
		//Nom de la catégorie de listIds, on affiche pas certaines listids (orange, signalement)
		if($sar['label'] != "Signalement" && $sar['label'] != "Orange"){
			echo "<td valign='top'>";
			echo "<table border=1 height='350px' class='tabTag'><tbody>";
			echo "<tr>";
		
			echo "<th><b>".$sar['label']."</b></th>";
			echo "</tr>";		
			for($i = 0; $i < count($sar["subLists"]);$i++){
				echo "<tr>";
				//Nom du listId
				echo "<td>".$sar["subLists"][$i]["label"]."</td>";
				//Nb correspondant
				echo "<td>".(isset($countChannel["lids"][$sar["subLists"][$i]["id"]]) ? $countChannel["lids"][$sar["subLists"][$i]["id"]] : "0")."</td>";
				echo "</tr>";
			}
		
			echo "</tbody></table>";
			echo "</td>";
		}
	}
	echo "</tr>";
	echo "</table>";
	
}else{
	echo "Aucune Stats pour cette période !";
}
$temps_fin = microtime(true);
echo "<br /	>";
echo 'Temps d\'execution : '.round($temps_fin - $temps_debut, 4);
?>
