<?php

/**
 * Project:     WCM
 * File:        biz.dashboardCustom.php
 *
 * @copyright   (c)2009 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system

require_once dirname(__FILE__).'/../../initWebApp.php';
require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');
ini_set('error_reporting', E_ERROR);
// Get current project
$project = wcmProject::getInstance();
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$siteId = $session->getSiteId();
$userGMT = $session->getUser()->timezone;

//$temps1 = microtime(true);
//$ArrayObjectsStored = wcmCache::fetch('ArrayObjectsStored');
//if (empty($ArrayObjectsStored))
//{
	$site = new site();
	// attention mise en cache activée !
//	$ArrayObjectsStored = $site->storeObjects(null,false,$siteId);
	$ArrayObjectsStored = $site->storeObjects(null,false);
//}
//$temps2 = microtime(true);
//wcmTrace('DASHBOARD : Temps ArrayObjectsStored : '.round($temps2 - $temps1 . "\n", 4));

// Retrieve REQUEST params
$itemId = getArrayParameter($_REQUEST, "itemId", 0);
$command      = getArrayParameter($_REQUEST, "command", null);
$destinationSiteId = getArrayParameter($_REQUEST, "destinationSiteId", 0);
$className = getArrayParameter($_REQUEST, "className", 0);

//duplicate object
if ($command == "duplicate" && !empty($itemId) && !empty($destinationSiteId) && !empty($className))
{
	header( 'Content-Type: text/xml' );
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	
	echo "<ajax-response>\n";
	echo "<response type='item' id='displayMsg'><![CDATA[";
	
	$object = new $className();
	$object->refresh($itemId);
	if ($object->duplicateObjetInSpecificSite($destinationSiteId))
		echo "<span style='left-margin:5px'> | --> Duplication done : ".$object->title."</span>";
	else
		echo "<span style='left-margin:5px'> | --> Duplication error : ".$object->title." (already exist)</span>";
			
	//echo $command." - ".$itemId." - ".$destinationSiteId." - ".$className;	
	echo "]]></response>\n";
	echo "</ajax-response>";
}
else if ($itemId == "browse")
{
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo "<ajax-response>\n";
echo "<response type='item' id='browseContent'><![CDATA[";

$querys = array();
$querys['news']  = "SELECT n.listIds,n.cId, n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source, n.sourceVersion, n.embedVideo, count( r.id ) AS nbRelations
FROM biz_news n
LEFT JOIN biz__relation r ON ( r.sourceId = n.Id
AND r.sourceClass = 'news'
AND r.destinationClass = 'photo' )
WHERE n.siteId = '$siteId' AND (n.workflowState = 'submitted' OR n.workflowState = 'approved' OR n.workflowState = 'draft')
GROUP BY n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source
ORDER BY UNIX_TIMESTAMP(n.publicationDate) DESC
LIMIT 0 , 100";

/*$querys['event']  = "SELECT n.id, n.title, n.startDate, n.channelId, n.workflowState, n.modifiedAt, n.source, count( r.id ) AS nbRelations
FROM biz_event n
LEFT JOIN biz__relation r ON ( r.sourceId = n.Id
AND r.sourceClass = 'event'
AND r.destinationClass = 'photo' )
WHERE n.siteId = '$siteId' AND (n.workflowState = 'submitted' OR n.workflowState = 'approved' OR n.workflowState = 'draft')
GROUP BY n.id, n.title, n.startDate, n.channelId, n.workflowState, n.modifiedAt, n.source
ORDER BY UNIX_TIMESTAMP(n.startDate) DESC
LIMIT 0 , 100";
*/

$querys['event']  = "SELECT n.id, n.title, n.startDate, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source, count( r.id ) AS nbRelations
FROM biz_event n
LEFT JOIN biz__relation r ON ( r.sourceId = n.Id
AND r.sourceClass = 'event'
AND r.destinationClass = 'photo' )
WHERE n.siteId = '$siteId' AND (n.workflowState = 'submitted' OR n.workflowState = 'approved' OR n.workflowState = 'draft')
GROUP BY n.id, n.title, n.startDate, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source
ORDER BY UNIX_TIMESTAMP(n.publicationDate) DESC
LIMIT 0 , 100";

$querys['slideshow']  = "SELECT n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source, count( r.id ) AS nbRelations
FROM biz_slideshow n
LEFT JOIN biz__relation r ON ( r.sourceId = n.Id
AND r.sourceClass = 'slideshow'
AND r.destinationClass = 'photo' )
WHERE n.siteId = '$siteId' AND (n.workflowState = 'submitted' OR n.workflowState = 'approved' OR n.workflowState = 'draft')
GROUP BY n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source
ORDER BY UNIX_TIMESTAMP(n.publicationDate) DESC
LIMIT 0 , 100";
//$querys['video']  = "SELECT `workflowState`, `title`, `channelId`, `id`, `modifiedAt`, `source` FROM `biz_video` WHERE `biz_video`.siteId='$siteId' AND `biz_video`.workflowState = 'submitted' OR `biz_video`.workflowState != 'draft' OR `biz_video`.workflowState = 'approved' ORDER BY UNIX_TIMESTAMP(modifiedAt) ASC LIMIT 0, 250";
$querys['video']  = "SELECT n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source, count( r.id ) AS nbRelations
FROM biz_video n
LEFT JOIN biz__relation r ON ( r.sourceId = n.Id
AND r.sourceClass = 'video'
AND r.destinationClass = 'photo' )
WHERE n.siteId = '$siteId' AND (n.workflowState = 'submitted' OR n.workflowState = 'approved' OR n.workflowState = 'draft')
GROUP BY n.id, n.title, n.publicationDate, n.channelId, n.workflowState, n.modifiedAt, n.source
ORDER BY UNIX_TIMESTAMP(n.publicationDate) DESC
LIMIT 0 , 100";

//$temps3 = microtime(true);

$db = new wcmDatabase($config['wcm.businessDB.connectionString']);
$acceptedWorkflows = array('approved', 'submitted', 'draft');
$myObjects = array('news' => array(),
                   'slideshow' => array(),
                   'event' => array(),
                   'video' => array());

foreach ($querys as $className => $query)
{
   $rs = $db->executeQuery($query);
   $rs->first();

   while($rec = $rs->getRow())
   {
      foreach ($acceptedWorkflows as $workflow)
      {
         if ($workflow == $rec['workflowState'])
         {
            $myObjects[$className][$workflow][] = $rec;
         }
      }

      $continue = $rs->next();
      if(!$continue) { break; }
   }
}

//$temps4 = microtime(true);
//wcmTrace('DB - Temps execution requete browse : '.round($temps4 - $temps3 . "\n", 4));

/*echo '<div id="browse_news" class="newSkins" style="display:block;"></div>';
echo '<div id="browse_slideshow" class="newSkins" style="display:block;"></div>';
echo '<div id="browse_event" class="newSkins" style="display:block;"></div>';
echo '<div id="browse_video" class="newSkins" style="display:block;">';
*/



foreach ($myObjects as $className => $rs)
{
   echo '<div id="browse_'.$className.'" class="newSkins" style="display:block;">';
   //echo "<h1>$className</h1>";

foreach ($acceptedWorkflows as $workflow)
{

   if (isset($rs[$workflow]))
   {
   echo '<h2><a href="#top" style="float:right; margin-right:20px;">Top</a><a name="'.$workflow.'"></a>'.$workflow.'</h2>';
?>

   <TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class="tableBordered">
    <TR>
        <TD>
           <b>Title</b>
        </TD>
	<TD>
	   <b>Vid</b>
	</TD>
        <TD>
           <b>Pics</b>
        </TD>
        <TD>
           <b>Rubric</b>
        </TD>
        <TD>
           <b>Main category</b>
        </TD>
        <TD>
           <b>Date & hour</b>
        </TD>
    </TR>

<?php
   foreach ($rs[$workflow] as $rec)
   {
      $theRubric = '';
      $thePilarRubric = '';     
      
      foreach ($ArrayObjectsStored[$siteId]["channel"] as $rubricId=>$rubricValue)
      {
      	if ($rubricId == $rec['channelId'])
         {
            $theRubric = $rubricValue['title'];
            $thePilarRubric = $rubricValue['parentTitle'];
            break;
         }
      }

      $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));

	if ($className == "event")
	{
      $datePublication = mktime(substr($rec['startDate'], 11, 2), substr($rec['startDate'], 14, 2), substr($rec['startDate'], 17, 2), substr($rec['startDate'], 5, 2), substr($rec['startDate'], 8, 2), substr($rec['startDate'], 0, 4));
	}
	else
	{
      $datePublication = mktime(substr($rec['publicationDate'], 11, 2), substr($rec['publicationDate'], 14, 2), substr($rec['publicationDate'], 17, 2), substr($rec['publicationDate'], 5, 2), substr($rec['publicationDate'], 8, 2), substr($rec['publicationDate'], 0, 4));
	}
?>


    <TR id="<?php echo $className."_browse_".$rec['id']; ?>">
        <TD width="46%">
           <a href="javascript:openDialog('business/pages/overview.php', 'class=<?php echo $className; ?>&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', '<?php echo $className; ?>', '<?php echo $rec['id']; ?>', '<?php echo $className."_browse_".$rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/<?php echo $className; ?>&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem">
	   <?php if ($rec['source'] == '10') { echo '<b style="color:gray">[a]</b>&nbsp;'; }
   	   		if ($rec['sourceVersion'] == 'extract') { echo '<b style="color:blue">[v]</b>&nbsp;'; }
   	   		
   			// on flag les univers d'origine des depeches dupliquées
           if (!empty($rec['cId'])) 
           { 
	           $indice = "";
	           $obj = new $className();
	           $obj->refresh($rec['cId']);
		        switch ($obj->siteId) 
		        {
				    /*case 4:
				        $indice =  "[eng]";
				        break;*/
				    case 5:
				        $indice =  "[fr]";
				        break;
				    case 6:
				        $indice =  "[fil]";
				        break;
				}       
				if (!empty($indice)) echo '<b style="color:black">'.$indice.'</b>&nbsp;'; 
           	}
   	   		
    	   if ($session->getSiteId() == 6 && $className == "news")
           {
           		if (isset($rec["listIds"]) && !empty($rec["listIds"]))
           		{
           			$tab = unserialize($rec["listIds"]);
           			if (in_array("1565", $tab)) echo '<b style="color:black">[mag]</b>&nbsp;'; 
           		}
           }
           	
            echo $rec["title"]; ?>
           </a>
        </TD>
	<TD width="1%">
	   <?php echo ($className == 'news' && $rec["embedVideo"] != 'NULL' && $rec["embedVideo"] != NULL) ? '1' : '0' ; ?>
	</TD>
        <TD width="3%">
          <?php echo $rec["nbRelations"]; ?>
        </TD>
        <TD width="15%">
           <?php if ($theRubric != '') echo getConst($theRubric); ?>
		   
        </TD>
        <TD width="15%">
           <?php if ($thePilarRubric != '') echo getConst($thePilarRubric)  ?>
        </TD>
        <TD width="15%">
           <?php echo date('D j F (H:i)', $datePublication); ?>
        </TD>
    </TR>


<?php
   }
   }
   echo '</TABLE>';
}
   echo "</div>";
}

//$temps3 = microtime(true);
//wcmTrace('DASHBOARD : Temps browseContent : '.round($temps3 - $temps2 . "\n", 4));


echo "]]></response>\n";
echo "</ajax-response>";
}
else if ($itemId == "published")
{
//$temps3 = microtime(true);
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo "<ajax-response>\n";
echo "<response type='item' id='publishedContent'><![CDATA[";

$querys = array();
$querys['news']  = "SELECT listIds,cId,source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 250";

//$querys['event']  = "SELECT source, `biz_event`.`startDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY startDate DESC LIMIT 0, 100";
$querys['event']  = "SELECT cId,source, `biz_event`.`startDate`, `biz_event`.`publicationDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";
// add by cc for events order by startDate
$querys['eventStart']  = "SELECT cId,source, `biz_event`.`startDate`, `biz_event`.`publicationDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY startDate DESC LIMIT 0, 100";

$querys['slideshow'] = "SELECT cId,source, `biz_slideshow`.`publicationDate`, `biz_slideshow`.`title`, `biz_slideshow`.`channelId`, `biz_slideshow`.`id` FROM `biz_slideshow` WHERE `biz_slideshow`.workflowState = 'published' AND `biz_slideshow`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";
$querys['video']  = "SELECT cId,source, `biz_video`.`publicationDate`, `biz_video`.`title`, `biz_video`.`channelId`, `biz_video`.`id` FROM `biz_video` WHERE `biz_video`.workflowState = 'published' AND `biz_video`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";

$querys['notice']  = "SELECT cId,`biz_notice`.`publicationDate`, `biz_notice`.`title`, `biz_notice`.`channelId`, `biz_notice`.`id` FROM `biz_notice` WHERE `biz_notice`.workflowState = 'published' AND `biz_notice`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 50";

$querys['afpENnews']  = "SELECT cId,source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='4' AND `biz_news`.cId IS NULL ORDER BY publicationDate DESC LIMIT 0, 100";
$querys['filFRnews']  = "SELECT listIds,cId,source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='6' AND `biz_news`.cId IS NULL ORDER BY publicationDate DESC LIMIT 0, 100";
$querys['afpENslideshow'] = "SELECT cId,source, `biz_slideshow`.`publicationDate`, `biz_slideshow`.`title`, `biz_slideshow`.`channelId`, `biz_slideshow`.`id` FROM `biz_slideshow` WHERE `biz_slideshow`.workflowState = 'published' AND `biz_slideshow`.siteId='4' AND `biz_slideshow`.cId IS NULL ORDER BY publicationDate DESC LIMIT 0, 100";
//$querys['afpFRnews']  = "SELECT cId,source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='5' AND `biz_news`.cId IS NULL ORDER BY publicationDate DESC LIMIT 0, 100";
$querys['afpFRnews']  = "SELECT cId,source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='5' ORDER BY publicationDate DESC LIMIT 0, 200";

$db = new wcmDatabase($config['wcm.businessDB.connectionString']);

$results = array();
foreach ($querys as $className => $query)
{
	$results[$className] = $db->executeQuery($query);	
}

$extractedResults = array();
$extractedNotices = array();

foreach ($results as $className => $rs)
{
	if ($className == 'afpENnews') $siteId = 4;
	else if ($className == 'filFRnews') $siteId = 6;
	else if ($className == 'afpENslideshow') $siteId = 4;
	else if ($className == 'afpFRnews') $siteId = 5;
	else $siteId = $session->getSiteId();
	
   	$rs->first();
   	
   	// tableau temporaire utilisé pour comparer les objetc afin d'éviter des doublons
   	$tempRec = array();

	while($rec = $rs->getRow())
   	{
   		foreach ($ArrayObjectsStored[$siteId]["channel"] as $rubricId=>$rubricValue)
      	{
      		//if ($className == 'afpENnews') echo "rubricId:".$rubricId." - channelId:".$rec['channelId']." - ".$rubricValue['parentId']." - ".$rec['channelId']."<br>"; 			
      		if ($rubricId == $rec['channelId'] || $rubricValue['parentId'] == $rec['channelId'])
		 	{
		 		if ($className != 'notice')
				{
					$theRubric = ($rubricValue['parentTitle'] == getConst("_RLX_RUBRICS")) ? $rubricValue['title'] : $rubricValue['parentTitle'];
		    		// on teste si la rubrique d'affectation n'est pas vide, si c'est une dépêche d'origine oui si c'est  un clone et si c'est le cas on vérifie que son id n'existe pas déjà dans le pilier
					//if (!empty($theRubric) && (empty($rec['cId']) || (!empty($rec['cId']) && !in_multiarray($rec['id'], $extractedResults[$className][$theRubric])))) 				
					
					// on teste si un objet identique n'exite pas déjà dans le tableau de données
					if (!empty($theRubric) && (!in_array($rec, $tempRec))) 
					{
						$extractedResults[$className][$theRubric][] = $rec;
						$tempRec[] = $rec;
					}						
		    	}
				else
				{
					$theRubric = ($rubricValue['parentTitle'] == getConst("_RLX_RUBRICS")) ? $rubricValue['title'] : $rubricValue['parentTitle'];
			    	// on teste si la rubrique d'affectation n'est pas vide, si c'est une dépêche d'origine oui si c'est  un clone et si c'est le cas on vérifie que son id n'existe pas déjà dans le pilier
					//if (!empty($theRubric) && (empty($rec['cId']) || (!empty($rec['cId']) && !in_multiarray($rec['id'], $extractedResults[$className][$theRubric]))))
					if (!empty($theRubric)) 
				    	$extractedNotices[$theRubric][] = $rec;
				}
			}
      	}
     
		$continue = $rs->next();
		if(!$continue) break; 
   	}
}

// on force la création du tableau si aucun objet pour éviter toute erreur JS
if (!isset($extractedResults['news'])) $extractedResults['news'] = array();
if (!isset($extractedResults['event'])) $extractedResults['event'] = array();
// add by cc for events order by startDate
if (!isset($extractedResults['eventStart'])) $extractedResults['eventStart'] = array();
if (!isset($extractedResults['slideshow'])) $extractedResults['slideshow'] = array();
if (!isset($extractedResults['video'])) $extractedResults['video'] = array();

if (!isset($extractedResults['afpENnews'])) $extractedResults['afpENnews'] = array();
if (!isset($extractedResults['filFRnews'])) $extractedResults['filFRnews'] = array();
if (!isset($extractedResults['afpENslideshow'])) $extractedResults['afpENslideshow'] = array();
if (!isset($extractedResults['afpFRnews'])) $extractedResults['afpFRnews'] = array();

// on stocke toutes les id des dépêche de l'univers courant
//$sql = 'SELECT cId FROM `biz_news` WHERE `biz_news`.workflowState=? AND `biz_news`.siteId=? AND `biz_news`.cId IS NOT NULL';
//$res = $db->executeQuery($sql, array("published", $session->getSiteId())); 
$sql = 'SELECT cId FROM `biz_news` WHERE `biz_news`.siteId=? AND `biz_news`.cId IS NOT NULL';
$res = $db->executeQuery($sql, array($session->getSiteId())); 
$currentNewsIds = array();
while ($res->next()) 
{
	$row = $res->getRow();
	$currentNewsIds[] = $row['cId'];
}       	

// on stocke toutes les cid des dépêches de l'univers AFP relax fr
$sql = 'SELECT cId FROM `biz_news` WHERE `biz_news`.siteId=? AND `biz_news`.cId IS NOT NULL';
$res = $db->executeQuery($sql, array(5)); 
$AFP_FR_NewsIds = array();
while ($res->next()) 
{
	$row = $res->getRow();
	$AFP_FR_NewsIds[] = $row['cId'];
}   


// on parcoure tous les items pour trier l'affichage suivant la rubrique d'appartenance
foreach ($extractedResults as $className => $allItems)
{
	// RAJOUT POUR FAIRE FONCTIONNER EN FRANCAIS
	$language = $_SESSION['wcmSession']->getLanguage();
   	if ($_SESSION['siteId'] == '5' || $_SESSION['siteId'] == '6') $language = 'fr'; 	  	
   	
   	// changement de la langue à la volée suite à la gestion d'affichages de données issues d'univers différents
   	if ($className == "afpENnews") $language = "en";
	else if ($className == "filFRnews") $language = "fr";
	else if ($className == "afpENslideshow") $language = "en";
	else if ($className == "afpFRnews") $language = "fr";
	
	echo '<div id="published_'.$className.'" class="newSkins"  style="display:block;">';

   	echo '<div class="columnAligned noSlide">';
    echo '<h2>'.strtoupper($GLOBALS['channelsByLanguage'][$language]['_RLX_WELLBEING']).'</h2>';
    echo '</div>';
    echo '<div class="columnAligned noSlide">';
    echo '<h2>'.strtoupper($GLOBALS['channelsByLanguage'][$language]['_RLX_HOUSEHOME']).'</h2>';
    echo '</div>';
    echo '<div class="columnAligned noSlide">';
    echo '<h2>'.strtoupper($GLOBALS['channelsByLanguage'][$language]['_RLX_ENTERTAINMENT']).'</h2>';
    echo '</div>';
    echo '<div class="columnAligned noSlide">';
    echo '<h2>'.strtoupper($GLOBALS['channelsByLanguage'][$language]['_RLX_TOURISM']).'</h2>';
    echo '</div>';
    echo '<div style="clear:both; height:2px;">&nbsp;</div>';
	
   	$allItemsSorted = array();
   	foreach ($allItems as $rubric => $items)
   	{
   		if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_WELLBEING'])
      	{  			
      		$allItemsSorted[0]["_RLX_WELLBEING"] = $items;  
      	}
      	else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_HOUSEHOME'])  
      	{		
      		$allItemsSorted[1]["_RLX_HOUSEHOME"] = $items;  
      	}
      	else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_ENTERTAINMENT']) 
      	{	
      		$allItemsSorted[2]["_RLX_ENTERTAINMENT"] = $items;  
      	}
      	else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_TOURISM']) 
      	{										
      		$allItemsSorted[3]["_RLX_TOURISM"] = $items;  
      	}
   	}

   	$noticeSorted = array();
	if ($className == 'news')
    {
		foreach ($extractedNotices as $rubric => $items)
   		{
   			if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_WELLBEING']) 
			{ 			
				$noticeSorted["_RLX_WELLBEING"] = $items; 
			} 
	      	else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_HOUSEHOME'])  	
	      	{	
	      		$noticeSorted["_RLX_HOUSEHOME"] = $items; 
	      	} 
	      	else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_ENTERTAINMENT'])  	
	      	{
	      		$noticeSorted["_RLX_ENTERTAINMENT"] = $items;  
	      	}
			else if ($rubric == $GLOBALS['channelsByLanguage'][$language]['_RLX_TOURISM'])  
			{ 										
				$noticeSorted["_RLX_TOURISM"] = $items; 
			} 
		}
    }
    
   //for ($i=0; $i<4; $i++)
   for ($i=0; $i<sizeof($allItemsSorted); $i++)
   {
		$GLOBALS['notices'] = '';
		if (isset($allItemsSorted[$i]))
		{
   		foreach ($allItemsSorted[$i] as $rubric => $items)
   		{
   			$currentDate = '';
			//$datePublicationFull = '';
			//$datePublicationFull_notice = '';
      		echo '<div class="columnAligned">';

      		echo "<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>"; 
	
      		foreach($items as $rec)
			{
				$temoin_notice = false;

			/*	if ($className == "event")
        		{
					$datePublication = mktime(0, 0, 0, substr($rec['startDate'], 5, 2), substr($rec['startDate'], 8, 2), substr($rec['startDate'], 0, 4));
                    $datePublicationFull = mktime(substr($rec['startDate'], 11, 2), substr($rec['startDate'], 14, 2), substr($rec['startDate'], 17, 2), substr($rec['startDate'], 5, 2), substr($rec['startDate'], 8, 2), substr($rec['startDate'], 0, 4));
        		}
        		else
        		{*/
					//$datePublicationFull_ancestor = ($datePublicationFull > $datePublicationFull_notice) ? $datePublicationFull : $datePublicationFull_notice;
					// dans le cas de eventStart la date qui fait référence n'est pas celle de publication mais celle de départ (startDate)
					if ($className == 'eventStart')
					{
						$datePublication = mktime(0, 0, 0, substr($rec['startDate'], 5, 2), substr($rec['startDate'], 8, 2), substr($rec['startDate'], 0, 4));
                		$datePublicationFull = mktime(substr($rec['startDate'], 11, 2), substr($rec['startDate'], 14, 2), substr($rec['startDate'], 17, 2), substr($rec['startDate'], 5, 2), substr($rec['startDate'], 8, 2), substr($rec['startDate'], 0, 4));
						
					}
					else
					{
						$datePublication = mktime(0, 0, 0, substr($rec['publicationDate'], 5, 2), substr($rec['publicationDate'], 8, 2), substr($rec['publicationDate'], 0, 4));
                		$datePublicationFull = mktime(substr($rec['publicationDate'], 11, 2), substr($rec['publicationDate'], 14, 2), substr($rec['publicationDate'], 17, 2), substr($rec['publicationDate'], 5, 2), substr($rec['publicationDate'], 8, 2), substr($rec['publicationDate'], 0, 4));
					}
					
					if ($className == 'news')
                    {
						if (!isset($datePublicationFull_ancestor)) { $datePublicationFull_ancestor = $datePublicationFull;  }
						if (!isset($last_notice)) { $last_notice = 0;  }
				
						foreach ($noticeSorted[$rubric] as $item_notice)
						{
	                    	$datePublication_notice = mktime(0, 0, 0, substr($item_notice['publicationDate'], 5, 2), substr($item_notice['publicationDate'], 8, 2), substr($item_notice['publicationDate'], 0, 4));
        	                $datePublicationFull_notice = mktime(substr($item_notice['publicationDate'], 11, 2), substr($item_notice['publicationDate'], 14, 2), substr($item_notice['publicationDate'], 17, 2), substr($item_notice['publicationDate'], 5, 2), substr($item_notice['publicationDate'], 8, 2), substr($item_notice['publicationDate'], 0, 4));

							$test = false;
							$stampOfDate = substr($item_notice['publicationDate'], 0 , 10);
							$stampOfDate2 = substr($rec['publicationDate'], 0 , 10);
							$idTemp = $item_notice['id'];
					
							if ($stampOfDate2 == $stampOfDate && $datePublicationFull_notice >= $datePublicationFull && !ereg("(#$idTemp#)", $GLOBALS['notices'], $regs))
							{
								$datePublicationFull_ancestor = $datePublicationFull_notice;
								//array_push($GLOBALS['notices'], $item_notice['id']);
								$GLOBALS['notices'] .= '#'.$item_notice['id'].'#';
								$last_notice = $item_notice['id'];
								$publicationDate_notice = $item_notice['publicationDate'];
								$title_notice = $item_notice['title'];
								$id_notice = $item_notice['id'];
								$temoin_notice = true;
								//print_r($GLOBALS['notices']);
								break;
							}
						}
            		}
       			//}

				$today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        		$todayFull = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));

				// NOTICE - notice
				if ($temoin_notice)
				{
					$dateStamp = $publicationDate_notice;
					if ($currentDate != substr($dateStamp, 0 , 10))
				    {
                		$currentDate = substr($dateStamp, 0 , 10);

                		if ($datePublication_notice > $today)
                		{
	                        echo '<TR>';
	                        echo '<TD colspan="4">';
	                        echo '<h3 style="color:#1C8E1C;">'.date('D j F', $datePublication_notice).'</h3>';
	                        echo '</td></tr>';
		                }
		                else
		                {
	                        echo '<TR>';
	                        echo '<TD colspan="4">';
	                        echo '<h3>'.date('D j F', $datePublication_notice).'</h3>';
	                        echo '</td></tr>';
		                }
           			}
           			
					if ($datePublicationFull_notice > $todayFull)
	                {
	                        $colorDate = 'style="color:#1C8E1C;" class="myclass_'.date('H:i', $datePublicationFull_notice).'_'.date('H:i', $todayFull).'"';
	                }
	                else
	                {
	                        $colorDate = '';
	                }
					
	                $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $title_notice));
?>
<TR id="<?php echo "notice_published_".$id_notice; ?>">
		<TD width='4%'>&nbsp;</TD>
        <TD width="4%">
                <a href="javascript:openDialog('business/pages/overview.php','class=notice&id=<?php echo $id_notice; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
        </TD>
        <TD width="82%">
                <a href="/index.php?_wcmAction=business/notice&id=<?php echo $id_notice; ?>" target="editNewItem" title="Edit" class="editItem">
                <span <?php echo $colorDate; ?>>NOTICE: <?php echo $title_notice; ?></span>
                </a>
        </TD>
        <TD width="5%">
                <?php echo date('H:i', $datePublicationFull_notice); ?>
        </TD>
    </TR>

<?php

				}
				// end NOTICE notice

				//$dateStamp = ($className == "event") ? $rec['startDate']  : $rec['publicationDate'];
				$dateStamp = $rec['publicationDate'];

		   		if ($currentDate != substr($dateStamp, 0 , 10))
		   		{
		   			$currentDate = substr($dateStamp, 0 , 10);
	
	                if ($datePublication > $today)
	                {
						echo '<TR>';
	        			echo '<TD colspan="4">';
						echo '<h3 style="color:#1C8E1C;">'.date('D j F', $datePublication).'</h3>';
	                    echo '</td></tr>';
	                }
	                else
	                {
						echo '<TR>';
	                    echo '<TD colspan="4">';
						echo '<h3>'.date('D j F', $datePublication).'</h3>';
						echo '</td></tr>';
	                }
		   		}
	   		
		   		if ($datePublicationFull > $todayFull)
				{
			        $colorDate = 'style="color:#1C8E1C;" class="myclass_'.date('H:i', $datePublicationFull).'_'.date('H:i', $todayFull).'"';
				}
				else
				{
					$colorDate = '';
				}
	
	      		$titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
?>

    <TR id="<?php echo $className."_published_".$rec['id']; ?>">
     	<?php 
        $bizClassName = $className;
        if ($className == "afpENnews") $bizClassName = "news";
		else if ($className == "filFRnews") $bizClassName = "news";
		else if ($className == "afpENslideshow") $bizClassName = "slideshow";
		else if ($className == "afpFRnews") $bizClassName = "news";
        else if ($className == "eventStart") $bizClassName = "event";
        
        $tagDuplicate = false;
        // add duplicate content icon
       
        if ($className == "afpENnews" || $className == "afpFRnews" || $className == "filFRnews" || $className == "afpENslideshow")
        {
        	// on vérifie que l'id courante n'existe pas dans le tableau des Cid des dépêches de l'univers courant
        	if (in_array($rec['id'], $currentNewsIds))
           	{ 
           		// le clone existe , on affiche  l'icone d'objet cloné
           		$tagDuplicate = true; 	       	
	        	echo "<TD width='4%'>&nbsp;</TD>";	
           	}
           	else 
           	{
           		// affiche l'icone de duplication si la news n'est pas déjà dupliqué et active le flag de duplication
           		?>
		        <TD width="4%">
		        <a href="javascript:if (confirm('Confirm duplication')){duplicateInCurrentUniverse(<?php  echo $session->getSiteId();?>, <?php  echo $rec['id'];?>, '<?php  echo $bizClassName;?>');}" title="Duplicate"><img src="/img/icons/content.gif" border="0"></a>&nbsp;
		        </TD>
	        	<?php 
           	}	
        } 
        
        else if ($className == "news" && ($session->getSiteId() == 6))
        {
        	// si on est dans l'univers Relaxfil et que l'on affiche l'onglet news, on affiche la possibilité de dupliquer vers l'univers AFP relax FR
        	
        	// on vérifie que l'id courante n'existe pas dans le tableau des Cid des dépêches de l'univers AFP Relax FR
        	if (in_array($rec['id'], $AFP_FR_NewsIds))
           	{ 
           		// le clone existe , on affiche  l'icone d'objet cloné
           		$tagDuplicate = true; 	       	
	        	echo "<TD width='4%'>&nbsp;</TD>";	
           	}
           	else 
           	{     	
        	?>
		        <TD width="4%">
		        <a href="javascript:if (confirm('Confirm duplication')){duplicateInCurrentUniverse(5, <?php  echo $rec['id'];?>, '<?php  echo $bizClassName;?>');}" title="Duplicate"><img src="/img/icons/content.gif" border="0"></a>&nbsp;
		        </TD>
	        <?php 
           	}
        }
        else
        	echo "<TD width='4%'>&nbsp;</TD>";	     	
        ?>
        <TD width="4%">      
		<a href="javascript:openDialog('business/pages/overview.php', 'class=<?php echo $bizClassName; ?>&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
        </TD>
        <TD width="82%">
		<a href="/index.php?_wcmAction=business/<?php echo $bizClassName; ?>&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem">
           <?php 
           
           if ($rec['source'] == '10') { echo '<b style="color:gray">[a]</b>&nbsp;'; } 
           
           if ($rec['sourceVersion'] == 'extract') { echo '<b style="color:blue">[v]</b>&nbsp;'; }
           
           // on flag les univers d'origine des depeches dupliquées
           if (!empty($rec['cId'])) 
           { 
           		$indice = "";
           		$obj = new $bizClassName();
           		$obj->refresh($rec['cId']);
		        switch ($obj->siteId) 
		        {
				    /*case 4:
				        $indice =  "[eng]";
				        break;*/
				    case 5:
				        $indice =  "[fr]";
				        break;
				    case 6:
				        $indice =  "[fil]";
				        break;
				}
           		
				if (!empty($indice)) echo '<b style="color:black">'.$indice.'</b>&nbsp;'; 
           }
           
           // detecte si la news est rattachée à la liste magazine (id: 1565)          
           if ($className == "filFRnews" || ($session->getSiteId() == 6 && $className == "news"))
           {
           		if (!empty($rec["listIds"]))
           		{
           			$tab = unserialize($rec["listIds"]);
           			if (in_array("1565", $tab)) echo '<b style="color:black">[mag]</b>&nbsp;'; 
           		}
           }
           
           // teste si la news existe déjà dans l'univers en cours pour les nouveaux onglets afpENnews, filFRnews & afpFRnews
           if ($tagDuplicate)
           		echo '<b style="color:red">[d]</b>&nbsp;';       
           ?>         
                <span <?php echo $colorDate; ?>><?php echo $rec["title"]; ?></span>
		</a>
        </TD>
        <TD width="5%">
		<?php echo date('H:i', $datePublicationFull); ?>
        </TD>
    </TR>

<?php	   
			}
	        echo '</table>';
	        echo '</div>';
   		}
		}
	}
	echo "</div>";
	echo "</div>";
}

//$temps4 = microtime(true);
//wcmTrace('DASHBOARD : Temps published : '.round($temps4 - $temps3 . "\n", 4));

echo "]]></response>\n";
echo "</ajax-response>";
exit();
}
else if ($itemId == "import")
{
   /*
 *
    Import ------------
 *
 */

   header( 'Content-Type: text/xml' );
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
   echo "<ajax-response>\n";
   echo "<response type='item' id='importContent'><![CDATA[";

   //$query = "SELECT `biz_news`.`workflowState`, `biz_news`.`createdAt`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id`, `biz_content`.`description` FROM `biz_news` LEFT JOIN `biz_content` ON `biz_news`.id = `biz_content`.referentId WHERE `biz_news`.workflowState = 'draft_import' AND import_feed='afp' AND `biz_news`.siteId='$siteId' LIMIT 0, 300";// AND DATE(createdAt) = DATE(NOW()) ORDER BY workflowState ASC LIMIT 0, 300";
   $query = "SELECT `biz_news`.`workflowState`, `biz_news`.`createdAt`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'draft_import' AND import_feed='afp' AND `biz_news`.siteId='$siteId' ORDER BY UNIX_TIMESTAMP(createdAt) DESC LIMIT 0, 150";
   $db = new wcmDatabase($config['wcm.businessDB.connectionString']);
   $rs = $db->executeQuery($query);
   $rs->first();
   $currentDate = '';

   echo '<div id="import_afp" class="newSkins" style="display:block; height:500px; overflow:scroll;">';
   echo "<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>";

   while($rec = $rs->getRow())
   {
        $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
        //$datePublication = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));

		$datePublication = mktime(0, 0, 0, substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
                $datePublicationFull = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
	
                $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $todayFull = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));


	   if ($currentDate != substr($rec['createdAt'], 0 , 10))
           {
                $currentDate = substr($rec['createdAt'], 0 , 10);

                        echo '<TR>';
                        echo '<TD colspan="3">';
                        echo '<h3>'.date('D j F', $datePublication).'</h3>';
                        echo '</td></tr>';
           }
?>

    <TR id="news_afp_<?php echo $rec['id']; ?>">
        <TD width="35%">
		<a href="javascript:saveImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', '15', 'news_afp_<?php echo $rec['id']; ?>');" title="Save" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -467px -232px no-repeat;"></a>
           <a href="javascript:openDialog('business/pages/overview.php','class=news&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', 'news_afp_<?php echo $rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/news&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem"><?php echo $rec["title"]; ?>
	</TD>
        <TD>
		<?php echo date('H:i', $datePublicationFull); ?>
        </TD>
    </TR>	

<?php
      $continue = $rs->next();
      if(!$continue) { break; }
   }

   echo '</table>';
   echo '</div>';

   // BEGIN ADD AFP VIDEO
   
   //$query = "SELECT workflowState, createdAt, title, channelId, id FROM biz_video WHERE workflowState='draft_import' AND import_feed='afp' AND siteId='$siteId' ORDER BY UNIX_TIMESTAMP(createdAt) DESC LIMIT 0, 150";
   $query = "SELECT `biz_video`.`workflowState`, `biz_video`.`createdAt`, `biz_video`.`title`, `biz_video`.`channelId`, `biz_video`.`id` FROM `biz_video` WHERE `biz_video`.workflowState = 'draft_import' AND import_feed='afp' AND `biz_video`.siteId='$siteId' ORDER BY UNIX_TIMESTAMP(createdAt) DESC LIMIT 0, 150";
   	  
   $rs = $db->executeQuery($query);
   $rs->first();
   $currentDate = '';
   echo '<div id="import_afp_video" class="newSkins" style="display:none; height:500px; overflow:scroll;">';
   echo "<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>";

   while($rec = $rs->getRow())
   {
        $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
        //$datePublication = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));

		$datePublication = mktime(0, 0, 0, substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
                $datePublicationFull = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
	
                $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $todayFull = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));


	   if ($currentDate != substr($rec['createdAt'], 0 , 10))
           {
                $currentDate = substr($rec['createdAt'], 0 , 10);

                        echo '<TR>';
                        echo '<TD colspan="3">';
                        echo '<h3>'.date('D j F', $datePublication).'</h3>';
                        echo '</td></tr>';
           }
?>

    <TR id="videos_afp_<?php echo $rec['id']; ?>">
        <TD width="35%">
		<a href="javascript:saveImport('<?php echo $titleCleaned; ?>', 'video', '<?php echo $rec['id']; ?>', '15', 'videos_afp_<?php echo $rec['id']; ?>');" title="Save" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -467px -232px no-repeat;"></a>
           <a href="javascript:openDialog('business/pages/overview.php', 'class=video&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', 'video', '<?php echo $rec['id']; ?>', 'videos_afp_<?php echo $rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/video&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem"><?php echo $rec["title"]; ?>
	</TD>
        <TD>
		<?php echo date('H:i', $datePublicationFull); ?>
        </TD>
    </TR>	

<?php
      $continue = $rs->next();
      if(!$continue) { break; }
   }

   echo '</table>';
   echo '</div>';
   
   //END AFP VIDEO
   
   
   if ($_SESSION['siteId'] == '5')
   {
	$query = "SELECT `biz_news`.`workflowState`, `biz_news`.`createdAt`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'draft_import' AND import_feed='relaxfil' AND `biz_news`.siteId='$siteId' ORDER BY UNIX_TIMESTAMP(createdAt) DESC LIMIT 0, 350";
   $db = new wcmDatabase($config['wcm.businessDB.connectionString']);
   $rs = $db->executeQuery($query);
   $rs->first();
   $currentDate = '';

	echo '<div id="import_fil" class="newSkins" style="display:block; height:500px; overflow:scroll;">';
   echo "<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>";

   while($rec = $rs->getRow())
   {
        $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
        //$datePublication = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));

                $datePublication = mktime(0, 0, 0, substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
                $datePublicationFull = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));

                $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $todayFull = mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y'));


           if ($currentDate != substr($rec['createdAt'], 0 , 10))
           {
                $currentDate = substr($rec['createdAt'], 0 , 10);

                        echo '<TR>';
                        echo '<TD colspan="3">';
                        echo '<h3>'.date('D j F', $datePublication).'</h3>';
                        echo '</td></tr>';
           }
		   
		   
?>

    <TR id="news_fil_<?php echo $rec['id']; ?>">
        <TD width="35%">
           <a href="javascript:saveImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', '15', 'news_fil_<?php echo $rec['id']; ?>');" title="Save" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -467px -232px no-repeat;"></a>
           <a href="javascript:openDialog('business/pages/overview.php', 'class=news&id=<?php echo $rec['id']; ?>','600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', 'news_fil_<?php echo $rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/news&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem">[
           		<?php 
				if ($rec["channelId"]) {
					$aChannel = new channel(null, $rec["channelId"]);
					echo $GLOBALS['channelsByLanguage']['fr'][$aChannel->title] ; 
					unset($aChannel);
				}
				
				?>] - <?php echo $rec["title"]; ?></a>
        </TD>
        <TD>
                <?php echo date('H:i', $datePublicationFull); ?>
        </TD>
    </TR>       

<?php
      $continue = $rs->next();
      if(!$continue) { break; }
   }

   echo '</table>';
   echo '</div>';
   }
  else 
{
?>
	<div id="import_fil" class="newSkins" style="display:block; height:500px; overflow:scroll;"></div>
<?php
}

   echo "]]></response>\n";
   echo "</ajax-response>";
}
else if ($itemId == "myactions")
{
   /*
 *
    Actions ------------
 *
 */

   header( 'Content-Type: text/xml' );
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
   echo "<ajax-response>\n";
   echo "<response type='item' id='actionsContent'><![CDATA[";

   $userId = $session->getUser()->id;

   $queryModified = array();
   $queryModified["news"] = "SELECT id, source, sourceVersion, modifiedAt, title FROM `biz_news` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 15";
   $queryModified["event"] = "SELECT id, source, modifiedAt, title FROM `biz_event` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 5";
   $queryModified["slideshow"] = "SELECT id, source, modifiedAt, title FROM `biz_slideshow` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 5";
   $queryModified["photo"] = "SELECT id, source, modifiedAt, title FROM `biz_photo` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 5";
   $queryModified["video"] = "SELECT id, source, modifiedAt, title FROM `biz_video` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 5";
   $queryModified["location"] = "SELECT id, source, modifiedAt, title FROM `biz_location` WHERE `modifiedBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(modifiedAt) DESC LIMIT 0, 5";

   $queryCreated = array();
   $queryCreated["news"] = "SELECT id, source, sourceVersion, createdAt, title FROM `biz_news` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 15";
   $queryCreated["event"] = "SELECT id, source, createdAt, title FROM `biz_event` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 5";
   $queryCreated["slideshow"] = "SELECT id, source, createdAt, title FROM `biz_slideshow` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 5";
   $queryCreated["photo"] = "SELECT id, source, createdAt, title FROM `biz_photo` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 5";
   $queryCreated["video"] = "SELECT id, source, createdAt, title FROM `biz_video` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 5";
   $queryCreated["location"] = "SELECT id, source, createdAt, title FROM `biz_location` WHERE `createdBy` = '$userId' AND siteId='$siteId' ORDER BY DATE(createdAt) DESC LIMIT 0, 5";

   $db = new wcmDatabase($config['wcm.businessDB.connectionString']);

   // modified
?>

   <div id="actions_modified" class="newSkins" style="display:block;">

   <TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>

<?php
foreach ($queryModified as $class => $query)
{

   $currentDate = '';
   $rsModified = $db->executeQuery($query);
   $rsModified->first();

   echo '<tr><td colspan="2"><h2>'.$class.'</h2></td></tr>';

   while($rec = $rsModified->getRow())
   {

        $datePublication = mktime(substr($rec['modifiedAt'], 11, 2), substr($rec['modifiedAt'], 14, 2), substr($rec['modifiedAt'], 17, 2), substr($rec['modifiedAt'], 5, 2), substr($rec['modifiedAt'], 8, 2), substr($rec['modifiedAt'], 0, 4));
      $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
?>

    <TR id="<?php echo $class; ?>_modified_<?php echo $rec['id']; ?>">
        <TD width="40%">
		<a href="javascript:openDialog('business/pages/overview.php', 'class=<?php echo $class; ?>&id=<?php echo $rec['id']; ?>','600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', '<?php echo $class; ?>_modified_<?php echo $rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/<?php echo $class; ?>&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem">
		<?php if ($rec['source'] == '10') { echo '<b style="color:gray">[a]</b>&nbsp;'; }
   		if ($rec['sourceVersion'] == 'extract') { echo '<b style="color:blue">[v]</b>&nbsp;'; }
		echo $rec["title"]; ?>
          </a>
        </TD>
        <TD>
		<?php echo date('D j F H:i:s', $datePublication); ?>
	</TD>
    </TR>


<?php
      $continue = $rsModified->next();
      if(!$continue) { break; }
   }
}
?>

</table>
</div>
<div id="actions_created" class="newSkins" style="display:block;">
<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>

<?php
// created

foreach ($queryCreated as $class => $query)
{
   $currentDate = '';
   $rsCreated = $db->executeQuery($query);
   $rsCreated->first();

   echo '<tr><td colspan="2"><h2>'.$class.'</h2></td></tr>';

   while($rec = $rsCreated->getRow())
   {
      $datePublication = mktime(substr($rec['createdAt'], 11, 2), substr($rec['createdAt'], 14, 2), substr($rec['createdAt'], 17, 2), substr($rec['createdAt'], 5, 2), substr($rec['createdAt'], 8, 2), substr($rec['createdAt'], 0, 4));
      $titleCleaned = trim(str_replace(array('\'', '`', '"'), array(' ', ' ', ' '), $rec['title']));
?>

    <TR id="<?php echo $class; ?>_modified_<?php echo $rec['id']; ?>">
        <TD width="40%">
		<a href="javascript:openDialog('business/pages/overview.php', 'class=<?php echo $class; ?>&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -465px -303px no-repeat;"></a>
           <a href="javascript:deleteImport('<?php echo $titleCleaned; ?>', 'news', '<?php echo $rec['id']; ?>', '<?php echo $class; ?>_created_<?php echo $rec['id']; ?>');" title="Delete" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(/img/Tango-feet.png) -320px -303px no-repeat;"></a>
           <a href="/index.php?_wcmAction=business/<?php echo $class; ?>&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem"><?php echo $rec["title"]; ?>
          </a>
        </TD>
	<TD>
		<?php echo date('D j F H:i:s', $datePublication); ?>
	</TD>
    </TR>

<?php
      $continue = $rsCreated->next();
      if(!$continue) { break; }
   }
}

echo '</table>';
echo '</div>';

   echo "]]></response>\n";
   echo "</ajax-response>";
}


exit();


header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo "<ajax-response>\n";
echo "<response type='item' id='frontContent'><![CDATA[";

?>

<div class="menuToogleDashboard">
      <a id="front_news_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">News</a>
      <a id="front_event_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">Event</a>
      <a id="front_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">Slideshow</a>
      <a id="front_video_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">Video</a>
      <div class="clearIt"></div>
   </div>

<div id="front_news" class="pannelRubric">
   <?php  include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/news/home/index.php'); ?>
   <div class="clearIt"></div>
</div>
<div id="front_event" class="pannelRubric">
   <?php include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/event/home/index.php'); ?>
   <div class="clearIt"></div>
</div>
<div id="front_slideshow" class="pannelRubric">
   <?php include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/slideshow/home/index.php'); ?>
   <div class="clearIt"></div>
</div>
<div id="front_video" class="pannelRubric">
   <?php include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/video/home/index.php'); ?>
   <div class="clearIt"></div>
</div>


<?php

echo "]]></response>\n";
echo "</ajax-response>";

exit();

/* CLEAN --------------------------------------------------------------------------------------------------*/

// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve parameters
$command      = getArrayParameter($_REQUEST, "command", null);
$exportRuleId = getArrayParameter($_REQUEST, "exportRuleId", 0);
$itemId       = getArrayParameter($_REQUEST, "itemId", 0);
$divId        = getArrayParameter($_REQUEST, "divId", 0);
$formDatas    = getArrayParameter($_REQUEST, "formDatas", null);

$msg = '';

$exportRule = new exportRule();
$exportRule->refresh($exportRuleId);

$formsDatasArray = array();
if ($formDatas)
{
	$groups = array();
	$temp = explode('&',$formDatas);
	foreach ($temp as $item)
	{
		$temp2 = explode('=',$item);
		$formsDatasArray[urldecode($temp2[0])] = urldecode($temp2[1]);
	}
	if ($formsDatasArray['type'])
	{
		switch ($formsDatasArray['type'])
		{
			case 'ftp':
				$arrayParameters = array(
					"host"           => $formsDatasArray['host'],
					"user"           => $formsDatasArray['user'],
					"pass"           => $formsDatasArray['pass'],
					"remotePath_ftp" => $formsDatasArray['remotePath_ftp']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
			case 'fs':
				$arrayParameters = array(
					"remotePath_fs" => $formsDatasArray['remotePath_fs']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
			case 'email':
				$arrayParameters = array(
					"fromName" => $formsDatasArray['fromName'],
					"fromMail" => $formsDatasArray['fromMail'],
					"to"       => $formsDatasArray['to'],
					"title"    => $formsDatasArray['title']
					);
				$formsDatasArray['connexionString'] = serialize($arrayParameters);
				break;
		}
	}
}

if ($command == 'insert' || $command =='update')
{
	if ($formsDatasArray['code'] == '')
	{
		$divId = "errorMsg";
		$msg = _CODE_MANDATORY;
	}
}

if ($msg == '')
{
	switch($command)
	{
		case "insert":
 		        $distributionChannel = new distributionChannel();
			$distributionChannel->bind($formsDatasArray);
			if(!$distributionChannel->save())
			{
				$divId = "errorMsg";
				$msg = $distributionChannel->getErrorMsg();
			}
			break;

		case "update":
 		        $distributionChannel = new distributionChannel();
 		        $distributionChannel->refresh($itemId);
			$distributionChannel->bind($formsDatasArray);
			if(!$distributionChannel->save())
			{
				$divId = "errorMsg";
				$msg = $distributionChannel->getErrorMsg();
			}
			break;

		case "delete":
			$distributionChannel = new distributionChannel();
			$distributionChannel->refresh($itemId);
			$distributionChannel->delete();
			break;
	}
}

// No browser cache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

// Xml output
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

// Write ajax response
echo "<ajax-response>\n";
echo "<response type='item' id='".$divId."'><![CDATA[";
if ($msg != '')
	echo "<div style=\"background-color:red; text-align: center; font-weight:bolder;\">".$msg."</div>";
else
{
	echo "<table id='distributionChannels'>";
	echo "<tr>";
	echo "<th width='30'>&nbsp;</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_CODE."</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_TYPE."</th>";
	echo "<th>"._DASHBOARD_MODULE_DISTRIBUTIONCHANNEL_ACTIVE."</th>";
	echo "<th>&nbsp;</th>";
	echo "</tr>";

        $typeList = distributionChannel::getTypeList();
        
        $distributionChannelsArray = $exportRule->getDistributionChannels(true);
        if (count($distributionChannelsArray)>0)
        {
 		$i=0;
       		foreach ($distributionChannelsArray as $currentDistributionChannel)
       		{
			if ($i%2==0)
				echo "<tr id='account_".$currentDistributionChannel->id."'>";
			else
				echo "<tr id='account_".$currentDistributionChannel->id."' class='alternate'>";
			echo "<td class='actions'>";
			echo "<ul class='two-buttons'>";
				echo "<li><a class='edit' title='"._EDIT."' href=\"javascript:openmodal('"._UPDATE_DISTRIBUTIONCHANNEL."','500'); modalPopup('distributionChannel','update', '".$currentDistributionChannel->id."', '".$exportRuleId."', '');\"><span>"._EDIT."</span></a></li>";
				echo "<li><a class='delete' title='"._DELETE."' href=\"javascript: if (confirm('"._DISTRIBUTIONCHANNEL_DELETE_CONFIRM."')) (ajaxDistributionChannel('delete', '".$exportRuleId."','".$currentDistributionChannel->id."', '".$divId."',''));\" id=''><span>"._DELETE."</span></a></li>";
			echo "</ul>";
			echo "</td>";
			echo "<td align='center'>";
				echo $currentDistributionChannel->code;
			echo "</td>";
			echo "<td align='center'>";
				echo $typeList[$currentDistributionChannel->type];
			echo "</td>";
			echo "<td align='center'>";
				if ($currentDistributionChannel->active)
					echo "<img src=img/grant.gif>";
				else
					echo "<img src=img/deny.gif>";
			echo "</td>";
			echo "<td align='center'>&nbsp;</td>";
			echo "</tr>";
			$i++;
		}
	}
	else
	{
		echo "<tr><td colspan='6'> - ("._EMPTY.") - </td></tr>";
	}
	echo "</table>";
}
echo "]]></response>\n";
echo "</ajax-response>";