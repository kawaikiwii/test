<?php	// Initialize system
		require_once dirname(__FILE__).'/initWebApp.php';
		//require_once dirname(__FILE__).'/business/languages/bizEn.php';
		//require_once dirname(__FILE__).'/business/languages/bizFr.php';
		//$session = wcmSession::getInstance();
		
                $temps_debut = microtime(true);
		//require_once dirname(__FILE__).'/globalsVars.php';
//		require_once(dirname( __FILE__ ).'/business/cron/init.php');
		$config						= wcmConfig::getInstance();
		$project 					= wcmProject::getInstance();	
		
		// Get Request parameters
		$default					= mktime(date('H'),date('i'),date('s'),date('m'),date('d') -7,date('Y'));
		$lang						= getArrayParameter($_REQUEST, "lang", "FR");
		$siteId						= getArrayParameter($_REQUEST, "siteId", 4);
		$from						= getArrayParameter($_REQUEST, "from", date('Y-m-d', $default)); //j-7
		$to							= getArrayParameter($_REQUEST, "to", date('Y-m-d')); //j
		$debug						= getArrayParameter($_REQUEST, "debug", 0);
		
		$site = new site($project, $siteId);
		
		//$ArrayObjectsStored = wcmCache::fetch('ArrayObjectsStored');
		//if (empty($ArrayObjectsStored))
			$ArrayObjectsStored = $site->storeObjects(null,false,$siteId);

		$language					= $site->language;
		//wcmSession::getInstance()->setLanguage($language);
		switch($siteId) {
			case 4:
                                $channelId_array = array(49,50,51,52);
                                $pilar = array("Entertainment","Well-being","House & Home","Tourism");
				$first = 49;
				$last = 52;
				break;
			case 5:
                                $channelId_array = array(1,2,3,4);
                                $pilar = array("Divertissement","Bien-être","Maison","Tourisme");
				$first = 1;
				$last = 4;
				break;
			case 6:
                                
                                $channelId_array = array(195,211,220,238);
                                $pilar = array("Divertissement","Bien-être","Maison","Tourisme");
				$first = 195;
				$last = 238;
				break;
		}
		//$first = ($siteId == 4) ? 49 : 1;
		//$last = ($siteId == 4) ? 52 : 4;
		
		
		$list						= array	(	
												"_THEMA_PRODUCT" => 123,
												"_NEWS_GEO_TARGETS_EUROPE" => 235,
												"_NEWS_GEO_TARGETS_ASIA" => 238,
												"_NEWS_GEO_TARGETS_NORTH_AMERICA" => 236,
												"_NEWS_GEO_TARGETS_INTERNATIONAL" => 242,
												"_THEMA_LUXURY" => 128,
												"_THEMA_TREND" => 242,
												"_THEMA_CELEBRITY" => 243,
												"_THEMA_OFFBEAT" => 1679,
												"_NEWS_TARGETS_CHILDREN"=>227,
												"_NEWS_TARGETS_TEENS"=>228,
												"_NEWS_TARGETS_SENIORS"=>229,
												"_NEWS_TARGETS_WOMEN" => 225,
												
											);
?>
<html>
	<head>
		<title>Global stats</title>
		<script type="text/javascript">AC_FL_RunContent = 0;</script>
		<script type="text/javascript"> DetectFlashVer = 0; </script>
		<!-- <script src="includes/charts/AC_RunActiveContent.js" type="text/javascript"></script>-->
		<script type="text/javascript">
		<!--
		var requiredMajorVersion = 10;
		var requiredMinorVersion = 0;
		var requiredRevision = 45;
		-->
		</script>
	</head>
	<body>
	
	
<!-- 
SELECT count( n.id ) , DATE_FORMAT( n.publicationDate, '%M' ) , c.title
FROM biz_news n
LEFT JOIN biz_channel c ON ( c.id = n.channelId )
WHERE n.workflowState = 'published'
AND n.siteId =4
AND YEAR( n.publicationDate ) =2010
AND c.title IS NOT NULL
GROUP BY MONTH( n.publicationDate ) , c.title
WITH ROLLUP
 -->
	
	
		<form id="form1" method="POST" action="global-stats.php">
			&nbsp;Site : <select name="siteId">
				<option value="4"<?php if($siteId==4) echo " SELECTED";?>>AFP/RELAXNEWS : English</option>
				<option value="5"<?php if($siteId==5) echo " SELECTED";?>>AFP/RELAXNEWS : Français</option>
				<option value="6"<?php if($siteId==6) echo " SELECTED";?>>RELAXFIL : Français</option>
			</select>
			<!--input type="text" name="siteId" value="<?//php echo $siteId;?>" /-->
			&nbsp;&nbsp;&nbsp;&nbsp;du (yyyy-mm-dd) : <input type="text" name="from" value="<?php echo $from;?>" style="width:70px" />
			&nbsp;&nbsp;&nbsp;&nbsp;au (yyyy-mm-dd) : <input type="text" name="to" value="<?php echo $to;?>" style="width:70px" />
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="Search" />
		</form>
		<hr/>
		<?php
		// Open database
		$db							= new wcmDatabase($config['wcm.businessDB.connectionString']);
		$systemDBPrefix				= $config['wcm.systemDB.databasePrefix'];
		$sqlFrom					= "";
		$sqlTo						= "";
		$titre						= "";
		if($from != '' && $from == $to) {
			$titre					= "Publications du " . date("d/m/Y", strtotime($from));
			$sqlFrom				= " AND DATE_FORMAT( n.publicationDate, '%Y-%m-%d' ) = '".$from."'";
		} else {
			if($from != '') {
				if($to != '') {
					$titre			= "Publications du ";
				} else {
					$titre			= "Publications depuis le ";
				}
				$titre				.= date("d/m/Y", strtotime($from));
				$sqlFrom			= " AND DATE_FORMAT( n.publicationDate, '%Y-%m-%d' ) >= '" . $from."'";
			}
			if($to != '') {
				if($from != '') {
					$titre			.= " au ";
				} else {
					$titre			.= "Publications jusqu'au ";
				}
				$titre				.= date("d/m/Y", strtotime($to));
				$sqlTo				= " AND DATE_FORMAT( n.publicationDate, '%Y-%m-%d' ) <= '" . $to."'";
			}
		}
		$strSite					= "";
		$sqlSite					= "";
		if($siteId != '') {
			$sqlSite				= " AND n.siteId = " . $siteId;
			$query					= "SELECT title FROM biz_site WHERE id=" . $siteId;
			$rs						= $db->executeQuery($query);
			$rs->first();
			$strSite				= "";
			while($rec = $rs->getRow()) {
				$strSite			= getConst($rec['title']);
				$continue = $rs->next();
				if(!$continue) { break; }
			}
		}
		
		?>
			<!-- <script type="text/javascript">
			
			if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
				alert("This page requires AC_RunActiveContent.js.");
			} else {
				var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
				if(hasRightVersion) { 
					AC_FL_RunContent(
						'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
						'width', '1060',
						'height', '350',
						'scale', 'noscale',
						'salign', 'TL',
						'bgcolor', '#777788',
						'wmode', 'opaque',
						'movie', 'charts',
						'src', 'includes/charts/charts',
						'FlashVars', 'library_path=includes/charts/charts_library&xml_source=includes/charts/test1.php', 
						'id', 'my_chart',
						'name', 'my_chart',
						'menu', 'true',
						'allowFullScreen', 'true',
						'allowScriptAccess','sameDomain',
						'quality', 'high',
						'align', 'middle',
						'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
						'play', 'true',
						'devicefont', 'false'
						); 
				} else { 
					var alternateContent = 'This content requires the Adobe Flash Player. '
					+ '<u><a href=http://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
					document.write(alternateContent); 
				}
			}
			
			</script>
			<noscript>
				<P>This content requires JavaScript.</P>
			</noscript>-->
			<?php 
		
		
		
		
		
		
		
		// Affichage des résultats
		echo "<span style='width:100%; text-align:center; font-size:20pt; font-weight:bold; text-decoration:underline;'>" . $titre . "</span>";
		if($strSite != '') {echo "<br/><span style='width:100%; text-align:center; font-size:20pt; text-decoration:underline;'>site : <strong>" . $strSite . "</strong></span>";}
			$query					= "SELECT l.label, count(DISTINCT n.id) as nbreNews, count(DISTINCT r.sourceId) as nbRelations FROM biz_news n INNER JOIN biz_channel c ON c.id=n.channelId AND c.siteId=n.siteId INNER JOIN " .$systemDBPrefix .".wcm_list l ON (l.id=n.source) LEFT JOIN biz__relation r ON (r.sourceId=n.Id AND r.sourceClass='news' AND r.destinationClass='photo') WHERE n.workflowState = 'published' AND n.source IS NOT Null AND n.siteId = ".$siteId.$sqlFrom.$sqlTo." GROUP BY l.label";
			$rs						= $db->executeQuery($query);
			$rs->first();

			?>
			<br/>
			<p style="color:blue">
				<?php 
					if($siteId==4) 
						echo "<strong>AFP/RELAXNEWS : English</strong>";
					elseif ($siteId==5)
						echo "<strong>AFP/RELAXNEWS : Français</strong>";
					elseif ($siteId==6)
						echo "<strong>RELAXFIL : Français</strong>";
				?>				
				<table border="1">
					<thead>
						<th width="150">Source</th>
						<th width="150">News</th>
						<th width="150">Illustrées</th>
					</thead>
					<tbody>
						<?php
						while($rec = $rs->getRow()) {
							echo "<tr>";
							echo "	<td>" . $rec['label'] . "</td>";
							echo "	<td align='center'>" . $rec['nbreNews'] . "</td>";
							echo "	<td align='center'>" . $rec['nbRelations'] . "</td>";
							echo "</tr>";
							$continue = $rs->next();
							if(!$continue) { break; }
						}
						?>
					</tbody>
				</table>
			</p>
			<?php if($debug == "1") echo $query;
		?>
		<br/>
		<p style="color:blue">
			<strong>News par pilier</strong>
			<table border="1">
				<thead>
					<th width="150">Pilier</th>
					<th width="150">News</th>
				</thead>
				<tbody>
					<?php
					
                                        foreach($pilar as $i){
                                        	$listRubricID		= "";
						$thePilarRubric		= "";
						foreach ($ArrayObjectsStored[$siteId]["channel"] as $key=>$rubric) 
						{
                                                        if ($rubric['parentTitle'] == $i) {
                                                            //print_r($rubric['parentId']." / ");
                                                            if($listRubricID != "") $listRubricID .= ",";
                                                            $listRubricID	.= $key;
                                                            $thePilarRubric	= $rubric['parentTitle'];
							}
						}
						//print_r($listRubricID);
						//print_r($thePilarRubric);
						if($listRubricID != "") {
							$query	= "SELECT count(DISTINCT n.id) as nbreNews FROM biz_news n INNER JOIN biz_channel c ON c.id=n.channelId AND c.siteId=n.siteId INNER JOIN " .$systemDBPrefix .".wcm_list l ON (l.id=n.source) WHERE n.workflowState = 'published' AND n.source IS NOT NULL AND channelId in (" . $listRubricID . ")" . $sqlSite . $sqlFrom . $sqlTo;
					                $rs		= $db->executeQuery($query);
							$rs->first();
							$rec = $rs->getRow();	//) {
//echo $query;
							echo "<tr>";
							echo "	<td>" . getConst($thePilarRubric) . "</td>";
							echo "	<td align='center'>" . $rec['nbreNews'];
//echo " (".$listRubricID.")";
							echo "	</td>";
							echo "</tr>";
							$rs->close();
						}
					}
					?>
				</tbody>
			</table>
		</p>
		
		<br/>
		<p style="color:blue">
			<strong>News par Flag</strong>
			<table border="1">
				<thead>
					<th width="150">Flag</th>
					<th width="150">News</th>
				</thead>
				<tbody>
					<?php
					foreach ($list as $name => $id) {
						$query		= "SELECT count(DISTINCT n.id) as nbreNews FROM biz_news n WHERE n.workflowState = 'published' AND n.listIds LIKE '%\"".$id."\"%'" . $sqlSite . $sqlFrom . $sqlTo;
						$rs			= $db->executeQuery($query);
						$rs->first();
						while($rec = $rs->getRow()) {
							echo "<tr>";
							echo "	<td>" . getConst($name) . "</td>";
							echo "	<td align='center'>" . $rec['nbreNews'] . "</td>";
							echo "</tr>";
							$continue = $rs->next();
							if(!$continue) { break; }
						}
						$rs->close();
					}
					?>
				</tbody>
			</table>
		</p>
		<?php if($debug == "1") echo $query;?>
		
		<br/>
		<p style="color:blue">
			<strong>News par rubrique</strong>
			<table border="1">
				<thead>
					<th width="150">Pilier</th>
					<th width="150">Rubrique</th>
					<th width="150">Principale</th>
				</thead>
				<tbody>
					<?php
                                        $query = "SELECT id,channelId FROM biz_news n WHERE n.workflowState = 'published' ". $sqlSite . $sqlFrom . $sqlTo;
                                        $rs = $db->executeQuery($query);
                                        $rs->first();
                                        //print_r($channelId_array);
                                        while($rec = $rs->getRow()) {

                                                //On initialise le cpt
                                                if(!isset($$rec["channelId"]))
                                                    $$rec["channelId"] = 0;
    
                                                $$rec["channelId"]++;
                                            
                                            $continue = $rs->next();
                                            if(!$continue) { break; }
                                        }
                                        $rs->close();
                                        
                                        foreach($channelId_array as $i){
                                            $oldTitle			= "";
                                            foreach ($ArrayObjectsStored[$siteId]["channel"] as $key=>$rubric) 
                                            {
                                                if ($rubric['parentId'] == $i) {
                                                    //echo $rubric['parentTitle']." : ".$key." : ".$rubric['title'].'<br />';
                                                    $theRubricID	= $key;
                                                    $theRubric		= $rubric['title'];
                                                    $thePilarRubric	= $rubric['parentTitle'];
                                                    echo "<tr>";
                                                    echo "<td>";
                                                    if($thePilarRubric != $oldTitle) echo getConst($thePilarRubric);
                                                    echo "</td>";
                                                    echo "<td>" . getConst($theRubric)."</td>";
                                                    
                                                    echo "<td>";
                                                    if(isset($$key))
                                                        echo $$key;
                                                    else
                                                        echo "0";
                                                    echo "</td>";
                                                    
                                                    echo "</tr>";

                                                    $oldTitle	= $thePilarRubric;
                                                }
                                            }
                                        }
					?>
				</tbody>
			</table>
		</p>
                
                <table border="1">
                    <thead>
                            <th width="150">Pilier</th>
                            <th width="150">Rubrique</th>
                            <th width="150">Secondaire</th>
                    </thead>
                    <tbody>
                            <?php
                                        $query2 = "SELECT id,channelId,channelIds FROM biz_news n WHERE n.workflowState = 'published' AND n.channelIds IS NOT NULL ". $sqlSite . $sqlFrom . $sqlTo;
                                        
                                        $rs2 = $db->executeQuery($query2);
                                        $rs2->first();
                                        while($rec2 = $rs2->getRow()) {
                                            
                                        	if($rec2["channelIds"] != "" || $rec2["channelIds"] != NULL){
	                                            $channelIds_array = unserialize($rec2["channelIds"]);
	                                            foreach($channelIds_array as $cids){
	                                            	if(!is_array($cids)){
	                                            		
		                                                if(!isset($$cids))
		                                                    $$cids = 0;
		                                                
		                                                if($cids != $rec2["channelId"])
		                                                    $$cids++;
	                                            	}
	                                            }                                            
                                        	}
                                            $continue = $rs2->next();
                                            if(!$continue) { break; }
                                        }
                                        $rs2->close();
                

                            foreach($channelId_array as $i){
                                foreach ($ArrayObjectsStored[$siteId]["channel"] as $key=>$rubric) 
                                {
                                    if ($rubric['parentId'] == $i) {
                                        $theRubricID	= $key;
                                        $theRubric		= $rubric['title'];
                                        $thePilarRubric	= $rubric['parentTitle'];
                                        echo "<tr>";
                                        echo "<td>";
                                        if($thePilarRubric != $oldTitle) echo getConst($thePilarRubric);
                                        echo "</td>";
                                        echo "<td>" . getConst($theRubric)."</td>";
                                        if(isset($$key))
                                        	echo "<td>" . $$key."</td>";
                                        else
                                        	echo "<td>0</td>";
                                        echo "</tr>";
                                        $oldTitle = $thePilarRubric;
                                    }
                                }
                            }   
                            ?>
                    </tbody>
                </table>
                
                
		<?php 
                if($debug == "1") echo $query;
                $temps_fin = microtime(true);
                echo 'Temps d\'execution : '.round($temps_fin - $temps_debut, 4);
                ?>
	</body>
</html>
