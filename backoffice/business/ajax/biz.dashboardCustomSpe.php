<?php

/**
 * Project:     WCM
 * File:        biz.dashboardCustom.php
 *
 * @copyright   (c)2011 Nstein Technologies
 * @version     4.x
 *
 */

// Initialize system

require_once dirname(__FILE__).'/../../initWebApp.php';
ini_set('error_reporting', E_ERROR);
// Get current project
$project = wcmProject::getInstance();
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$siteId = $session->getSiteId();
$userGMT = $session->getUser()->timezone;

//$ArrayObjectsStored = wcmCache::fetch('ArrayObjectsStored');
//if (empty($ArrayObjectsStored))
//{
	$site = new site();
	// attention mise en cache activée !
	$ArrayObjectsStored = $site->storeObjects(null,false,$siteId);
//}
    	

// Retrieve REQUEST params
$itemId = getArrayParameter($_REQUEST, "itemId", 0);
if ($itemId == "published")
{
header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo "<ajax-response>\n";
echo "<response type='item' id='publishedContent'><![CDATA[";

$querys = array();
$querys['news']  = "SELECT source,sourceVersion, `biz_news`.`publicationDate`, `biz_news`.`title`, `biz_news`.`channelId`, `biz_news`.`id` FROM `biz_news` WHERE `biz_news`.workflowState = 'published' AND `biz_news`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 250";

//$querys['event']  = "SELECT source, `biz_event`.`startDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY startDate DESC LIMIT 0, 100";
$querys['event']  = "SELECT source, `biz_event`.`startDate`, `biz_event`.`publicationDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";
// add by cc for events order by startDate
$querys['eventStart']  = "SELECT source, `biz_event`.`startDate`, `biz_event`.`publicationDate`, `biz_event`.`title`, `biz_event`.`channelId`, `biz_event`.`id` FROM `biz_event` WHERE `biz_event`.workflowState = 'published' AND `biz_event`.siteId='$siteId' ORDER BY startDate DESC LIMIT 0, 100";

$querys['slideshow'] = "SELECT source, `biz_slideshow`.`publicationDate`, `biz_slideshow`.`title`, `biz_slideshow`.`channelId`, `biz_slideshow`.`id` FROM `biz_slideshow` WHERE `biz_slideshow`.workflowState = 'published' AND `biz_slideshow`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";
$querys['video']  = "SELECT source, `biz_video`.`publicationDate`, `biz_video`.`title`, `biz_video`.`channelId`, `biz_video`.`id` FROM `biz_video` WHERE `biz_video`.workflowState = 'published' AND `biz_video`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 100";

$querys['notice']  = "SELECT `biz_notice`.`publicationDate`, `biz_notice`.`title`, `biz_notice`.`channelId`, `biz_notice`.`id` FROM `biz_notice` WHERE `biz_notice`.workflowState = 'published' AND `biz_notice`.siteId='$siteId' ORDER BY publicationDate DESC LIMIT 0, 50";

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
   	$rs->first();

	while($rec = $rs->getRow())
   	{
   		foreach ($ArrayObjectsStored[$siteId]["channel"] as $rubricId=>$rubricValue)
      	{
      		if ($rubricId == $rec['channelId'] || $rubricValue['parentId'] == $rec['channelId'])
		 	{
		 		if ($className != 'notice')
				{
					$theRubric = $rubricValue['parentTitle'];
		    		$extractedResults[$className][$theRubric][] = $rec;
		    	}
				else
				{
					$theRubric = $rubricValue['parentTitle'];
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

//print_r($extractedResults);

// RAJOUT POUR FAIRE FONCTIONNER EN FRANCAIS
   	$language = $_SESSION['wcmSession']->getLanguage();
   	if ($_SESSION['siteId'] == '11') $language = 'fr'; 	  	
   
foreach ($extractedResults as $className => $allItems)
{
	echo '<div id="published_'.$className.'" class="newSkins"  style="display:block;">';

   	/*echo '<div class="columnAligned noSlide">';
    echo '<h2>'.strtoupper($GLOBALS['channelsByLanguage'][$language]['_RLX_WELLBEING']).'</h2>';
    echo '</div>';*/
    echo '<div style="clear:both; height:2px;">&nbsp;</div>';
	
   	$allItemsSorted = array();
   	foreach ($allItems as $rubric => $items)
   	{
   		$allItemsSorted[0]["_RLX_WELLBEING"] = $items;  
   	}

   	$noticeSorted = array();
	if ($className == 'news')
    {
		foreach ($extractedNotices as $rubric => $items)
   		{
   			$noticeSorted["_RLX_WELLBEING"] = $items; 
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
      		//echo '<div class="columnAligned">';
			echo '<div>';
      		
      		echo "<TABLE WIDTH='100%' CELLSPACING='0' CELLPADDING='5' ALIGN='left' class='tableBordered'>"; 			 
		    echo "<TR>
		        <TD colspan='2'>
		           <b>Title</b>
		        </TD>
		        <TD>
		           <b>Time</b>
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
		    </TR>";
		    
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
        <TD width="2%">
                <a href="javascript:openDialog('business/pages/overview.php','class=notice&id=<?php echo $id_notice; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
        </TD>
        <TD width="50%">
                <a href="/index.php?_wcmAction=business/notice&id=<?php echo $id_notice; ?>" target="editNewItem" title="Edit" class="editItem">
                <span <?php echo $colorDate; ?>>NOTICE: <?php echo $title_notice; ?></span>
                </a>
        </TD>
        <TD width="5%">
                <?php echo date('H:i', $datePublicationFull_notice); ?>
        </TD>
        <TD><?php echo ($className == 'news' && $rec["embedVideo"] != 'NULL' && $rec["embedVideo"] != NULL) ? '1' : '0' ; ?></TD>
        <TD>
        <?php 
        	$object = new $className();
        	$object->refresh($rec["id"]);
        	$relateds = $object->getRelatedsByClassAndKind("photo");
        	echo count($relateds);
        ?>
        </TD>
        <TD> 
        <?php 
			if (!empty($rec["channelId"]))
			{
				$channel = new channel();
				$channel->refresh($rec["channelId"]);
				echo $channel->title;
			}
        ?>
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
        <TD width="2%">
		<a href="javascript:openDialog('business/pages/overview.php', 'class=<?php echo $className; ?>&id=<?php echo $rec['id']; ?>', '600','800','600','800','');" title="View (pop up)" style="display:block; float:left; margin-right:5px; border:1px solid #FFF; height:17px; width:20px; background: url(http://dev.bo.afprelax.net/img/Tango-feet.png) -465px -303px no-repeat;"></a>
        </TD>
        <TD width="50%">
        <?php 
        // cas specifique du 2ème onglet Event
        $bizClassName = $className;
        if ($bizClassName == "eventStart") $bizClassName = "event";
        ?>
		<a href="/index.php?_wcmAction=business/<?php echo $bizClassName; ?>&id=<?php echo $rec['id']; ?>" target="editNewItem" title="Edit" class="editItem">
           <?php if ($rec['source'] == '10') { echo '<b>[a]</b>&nbsp;'; } 
           if ($rec['sourceVersion'] == 'extract') { echo '<b style="color:blue">[v]</b>&nbsp;'; }?>
                <span <?php echo $colorDate; ?>><?php echo $rec["title"]; ?></span>
		</a>
        </TD>
        <TD width="5%">
		<?php echo date('H:i', $datePublicationFull); ?>
        </TD>
        <TD><?php echo ($className == 'news' && $rec["embedVideo"] != 'NULL' && $rec["embedVideo"] != NULL) ? '1' : '0' ; ?></TD>
        <TD>
        <?php 
        	$object = new $className();
        	$object->refresh($rec["id"]);
        	$relateds = $object->getRelatedsByClassAndKind("photo");
        	echo count($relateds);
        ?>
        </TD>
        <TD> 
        <?php 
			if (!empty($rec["channelId"]))
			{
				$channel = new channel();
				$channel->refresh($rec["channelId"]);
				echo $channel->title;
			}
        ?>
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


echo "]]></response>\n";
echo "</ajax-response>";
exit();
}

exit();


header( 'Content-Type: text/xml' );
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

echo "<ajax-response>\n";
echo "<response type='item' id='frontContent'><![CDATA[";

?>

<div class="menuToogleDashboard">
      <a id="front_news_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">News</a>
      <a id="front_slideshow_link" class="passiveState" href="javascript:void(0)" onClick="switchPannel(this, 'front')">Slideshow</a>
      <div class="clearIt"></div>
   </div>

<div id="front_news" class="pannelRubric">
   <?php  include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/news/home/index.php'); ?>
   <div class="clearIt"></div>
</div>

<div id="front_slideshow" class="pannelRubric">
   <?php include_once($config['wcm.webSite.path'].$_SESSION['wcmSession']->getLanguage().'/slideshow/home/index.php'); ?>
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
