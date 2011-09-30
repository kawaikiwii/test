<?php 
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html;charset=UTF-8');

require_once (dirname(__FILE__).'/conf/config.php');
require_once (dirname(__FILE__).'/../../inc/wcmInit.php');

$CURRENT_SITECODE = (defined(CURRENT_SITECODE)) ? CURRENT_SITECODE : "en";
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
            Sorry, your “relax” access doesn’t include this service yet.
        </h1>
        <?php
        $subject = "I would like to subscribe to new AFP Relaxnews services";
        if (strpos("@afp.com", $CURRENT_ACCOUNT_MANAGER->email) > 0) {
            
        ?>
        <p>
            To subscribe it, please contact us : <a href="mailto:<?php echo $CURRENT_ACCOUNT_MANAGER->email; ?>?subject=<?php echo $subject; ?>"><?php echo $CURRENT_ACCOUNT_MANAGER->email; ?></a>
        </p>
        <?php 
        } elseif (strpos("@relaxnews.com", $CURRENT_ACCOUNT_MANAGER->email) > 0) {
            
        ?>
        <p>
            To subscribe it, please contact us : <a href="mailto:marketing@relaxnews.com?subject=<?php echo $subject; ?>">marketing@relaxnews.com / +33 1 53 19 89 70</a>
        </p>
        <?php 
        } else {
            
        ?>
        <p>
            To subscribe it, please contact us : <a href="mailto:contact@afprelaxnews.com?subject=<?php echo $subject; ?>">contact@afprelaxnews.com</a>
        </p>
        <?php 
        }
        ?>
    <div class="ari-restricted-access">
		<h1>See which services you currently have access to :</h1>
		<?php 
        $FOpermissions = array();
        $permissions = $CURRENT_ACCOUNT->getPermissions();
        
        $serv_news = "";
        $serv_event = "";
        $serv_slideshow = "";
        $serv_video = "";
        if ($site->isAllowed()) {
            $services = $CURRENT_ACCOUNT->getServices($site->id);
            foreach ($services as $service) {
                if ($service == "event")
                    $serv_event = getConst(_BIZ_EVENTS);
                else if ($service == "news")
                    $serv_news = getConst(_BIZ_NEWSS);
                else if ($service == "video")
                    $serv_video = getConst(_BIZ_VIDEOS);
                else if ($service == "slideshow")
                    $serv_slideshow = getConst(_BIZ_SLIDESHOWS);
            }
        }
        if(!empty($serv_news))
        	$FOpermissions[$serv_news]      = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "news");
        if(!empty($serv_event))
        	$FOpermissions[$serv_event]     = $CURRENT_ACCOUNT->getRubriques($session->getSiteId(), "event");
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
        	$channel = new channel();
			foreach ($perm as $per)
        	{
        		$channel->refresh($per);
        		if (!empty($channel->parentId))
            		$tempArray[$channel->parentId][] = $channel->title;
        	}
        	
        	if (!empty($tempArray))
        	{
        		ksort($tempArray);
        		$channelTemp = new channel();
        		foreach ($tempArray as $key => $value)
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
							echo '<span style="margin-right:15px">All topics</span>';
					}
					echo "</li>";
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
