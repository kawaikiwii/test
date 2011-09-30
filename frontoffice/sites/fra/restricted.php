<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "fra";
$DISABLED_ACCESS = false;
$ANNOUNCE_MAINTENANCE = false;
require_once (dirname(__FILE__).'/../../inc/siteInit.php');

if (!isset($session->userId)) {
    header('Location: '.$site->url);
    exit();
}

ob_start("ob_gzhandler");
?>
<div class="ari-restricted">
    <div class="ari-restricted-infos">
        <h1 class="ari-restricted-title">
            Désolé, votre relax accès n’inclut pas - encore - ce service.
        </h1>
        <p>
            Pour vous y abonner, merci de contacter : <a href="mailto:marketing@relaxnews.com?subject=Je souhaite m’abonner à des services supplémentaires du Relaxfil">marketing@relaxnews.com</a>
            / +33 1 53 19 89 70 
        </p>
    </div>
    <div class="ari-restricted-access">
		<h1>Rappel des services auxquels vous avez actuellement accès :</h1>
		<?php 
        $FOpermissions = array();
        $permissions = $CURRENT_ACCOUNT->getPermissions();
        
        $serv_news = "";
        $serv_prevision = "";
        $serv_slideshow = "";
        $serv_video = "";
        if ($site->isAllowed()) {
            $services = $CURRENT_ACCOUNT->getServices($site->id);
            foreach ($services as $service) {
            	if($service != "notice" && $service != "event") {
	                if ($service == "news")
	                    $serv_news = getConst(_BIZ_NEWSS);
	                else if ($service == "video")
	                    $serv_video = getConst(_BIZ_VIDEOS);
	                else if ($service == "slideshow")
	                    $serv_slideshow = getConst(_BIZ_SLIDESHOWS);
	                else if ($service == "prevision")
	                    $serv_prevision = getConst(_BIZ_PREVISION);
            	}
            }
        }
        if(!empty($serv_news))
        	$FOpermissions[$serv_news]      = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "news");
        if(!empty($serv_prevision))
        	$FOpermissions[$serv_prevision]  = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "prevision");
        if(!empty($serv_slideshow))
        	$FOpermissions[$serv_slideshow] = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "slideshow");
        if(!empty($serv_video))
        	$FOpermissions[$serv_video]     = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "video");
        
        foreach ($FOpermissions as $service=>$perm)
        {
        $tempArray = array();
        ?>
        <div class="ari-restricted-access-service">
        	<h4><?php echo $service?></h4>
        	<ul class="ari-restricted-access-rubrics">
        	<?php
        	if($service == $serv_prevision)
        		echo '<li style="clear:both"><span style="margin-right:15px">Toutes les rubriques</span></li>';
        	else {
        		$channel = new channel();
				foreach ($perm as $per)
	        	{
	        		$channel->refresh($per);
	        		if (!empty($channel->parentId))
	            		$tempArray[$channel->parentId][] = $channel->title;
	        	}
	        	
	        	if (!empty($tempArray))
	        	{
	        		$channelTemp = new channel();
	        		foreach (array_reverse($tempArray,true) as $key => $value)
	        		{
	        			$channelTemp->refresh($key);
	        			echo '<li style="clear:both"><em><i>'.$channelTemp->title.'</i> : </em>';
	        			if (!empty($value))
						{
							$nbRubriques = $CURRENT_ACCOUNT->getNbRubriquesByPilier($key);
							if($nbRubriques != count($value)) {
								sort($value);
								foreach($value as $val)
									echo '<span style="margin-right:15px">'.$val.'</span>';
							}
							else
								echo '<span style="margin-right:15px">Toutes les rubriques</span>';
						}
						echo "</li>";
					}
	        	}
        	}
        	?>
        	</ul>
        </div>
        <?php
        }
        ?>
    </div>
</div>
<?php
ob_flush();
?>
