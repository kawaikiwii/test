<?php 
/**
 * Project:     WCM
 * File:        dialogs/generate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 * Dialogs enabling generation lauching and monitoring
 * Generations are launch in background (using task manager)
 */
 
// Initialize system
require_once dirname(__FILE__).'/../../initWebApp.php';
//include(WCM_DIR . '/pages/includes/header_popup.php');

$project = wcmProject::getInstance();

$bizobject = wcmMVC_Action::getContext();
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$site = new site();
$site->refresh($session->getSiteId());

$enumSite = new site();
if ($enumSite->beginEnum()) {
    while ($enumSite->nextEnum()) {
        if (($session->isAllowed($enumSite, wcmPermission::P_WRITE)) && ($enumSite->id != $site->id)) {
            $allSites["$enumSite->title"] = $enumSite->id;
        }
    }
    $enumSite->endEnum();
}
unset($enumSite);

$allSites["$site->title"] = $site->id;

?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Duplication</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <?php 
        include (WCM_DIR.'/js/main.js.php');
        ?>
        <script>
            function timeclose(){
                setTimeout("saveMyObject()", 1000);
            }
        </script>
    </head>
    <?php 
    if (isset($_GET['action']) && $_GET['action']) {
        echo "<script type=\"text/javascript\">
        				function saveMyObject()
        				{
        					var wcmActionController  = new WCM.ActionController();
        					wcmActionController.triggerEvent('save',{});
        				}
        			</script>";
        echo "<body onLoad=timeclose()> <blink>Saving...</blink>";
    } else
        echo "<body>";
    ?>
    <?php 
    // display list of available generation rules
    echo '<div class="description">';
    echo '<h2>Duplication
        	<img src="/img/flag_'.$_SESSION['wcmSession']->getSite()->language.'.jpg" alt="'.strtoupper($_SESSION['wcmSession']->getSite()->language).'" width="50" height="25" style="position:absolute; margin-top:-8px; margin-left:30px;" />
        	<br />
        	<b style="color:red">'.strtoupper($bizobject->getClass()).'</b> &nbsp;<small>"'.substr($bizobject->title, 0, 60).'(...)"</small></h2><br /><br /><br /><br /><br />';
        	
    if (isset($_GET['action']) && $_GET['action']) {
        wcmGUI::openObjectForm($bizobject);
        echo '<input type="hidden" name="chooseLanguage" value="'.$_GET['chooseLanguage'].'" />';
        echo '<input type="hidden" name="userAssign" value="'.$_GET['userAssign'].'" />';
        echo '</form>';
    }
    
    if (!isset($_GET['action']) && !isset($_GET['message'])) {
    
        echo '<form name="wcmForm" id="wcmForm" action="#" method="get">';
        echo '<table cellspacing="0" cellpadding="10"><tr><td>'._DUPLICATION_LANGUAGE.'</td><td>';
        echo '<input type="hidden" name="action" value="true" />';
        // Retrieve all duplicated Object associated to the current object
        $idToCheck = ($bizobject->cId != NULL) ? array($bizobject->cId) : array($bizobject->id);
        $duplicatedObjects = (sizeof($duplicated = $bizobject->getAllDuplicatedObjectsFromIds($idToCheck)) > 0) ? $duplicated : array();
        $sitesIdsUsed = array();
        
        foreach ($duplicatedObjects as $duplicatedObject) {
            foreach ($duplicatedObject as $duplicatedObjectAssociated) {
                $sitesIdsUsed[] = $duplicatedObjectAssociated['siteId'];
            }
        }
        
        $db = new wcmDatabase($config['wcm.systemDB.connectionString']);
        $query = "SELECT * FROM `wcm_user` WHERE isAdministrator='0'";
        $results = $db->executeQuery($query);
        
        echo '<select name="chooseLanguage" id="chooseLanguage">';
        
        foreach ($allSites as $siteLanguage=>$siteId) {
            echo '<option value="'.$siteId.'">'.strtoupper($siteLanguage).'</option>';
        }
        echo '</select>';
        
        echo '</td></tr><tr><td>'._DUPLICATION_USER.'</td><td>';
        
        echo '<select name="userAssign" id="userAssign">';
        echo '<option value="1">(none)</option>';
        
        foreach ($project->membership->getUsersOfGroup(7) as $user) {
            echo '<option value="'.$user->id.'">'.$user->name.'</option>"';
        }
        
        echo '</select>';
        
        echo '</td></tr><tr><td></td><td>';
        echo '<input type="submit" value="Duplicate">';
        echo '</td></tr></table>';
        echo '</form>';
    }
    
    if (isset($_GET['message'])) {
        if ($_GET['message']) {
            if ($_SESSION['wcmSession']->getSite()->language == 'en')
                echo '<div id="messageHere" style="padding:5px; font-size:14px;"><b style="color:green;">Object duplicated successfully.</b><br /><a href="javascript:void(0);" onClick="opener.location = \''.$config['wcm.backOffice.url'].'index.php?_wcmAction=business/'.$bizobject->getClass().'&id='.$_GET['urlId'].'\'; window.close();" style="color:blue; text-decoration:underline;">Go to duplicated Object</a> or <a href="?"  style="color:blue; text-decoration:underline;">Launch a new duplication</a></div>';
            else
                echo '<div id="messageHere" style="padding:5px; font-size:14px;"><b style="color:green;">Objet du dupliqué avec succès.</b><br /><a href="javascript:void(0);" onClick="opener.location = \''.$config['wcm.backOffice.url'].'index.php?_wcmAction=business/'.$bizobject->getClass().'&id='.$_GET['urlId'].'\';  window.close();" style="color:blue; text-decoration:underline;">Utiliser maintenant l\'objet dupliqué</a> or <a href="?"  style="color:blue; text-decoration:underline;">Lancer une autre duplication</a></div>';
        } else {
            if ($_SESSION['wcmSession']->getSite()->language == 'en')
                echo '<div id="messageHere" style="padding:5px; font-size:14px;"><b style="color:red;">Duplication failed.</b><br /> <a href="?" style="color:blue; text-decoration:underline;">Retry</a></div>';
            else
                echo '<div id="messageHere" style="padding:5px; font-size:14px;"><b style="color:red;">La duplication a échouée.</b><br /> <a href="?" style="color:blue; text-decoration:underline;">Recommencer</a></div>';
        }
    }
    
    include (WCM_DIR.'/pages/includes/footer.php');
