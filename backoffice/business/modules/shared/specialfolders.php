<?php
 /* File:        modules/shared/specialfolders.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    require_once(WCM_DIR.'/business/api/toolbox/biz.relax.toolbox.php');

    /* IMPORTANT !! Utile car on perd les infos si on upload des photos */
	if(isset($_SESSION['wcmActionMain']) && $_SESSION['wcmAction'] != $_SESSION['wcmActionMain'])
    	$_SESSION['wcmAction'] = $_SESSION['wcmActionMain'];
    
    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();

    $_SESSION['wcm']['footprint']['context'] = $bizobject;


    echo '<div class="zone">';

    /*
     * Display Special folders
     */
    wcmGUI::openCollapsablePane(_RLX_SPECIALFOLDERS);
    wcmGUI::renderHiddenField('tab_specialfolders', "1");
	echo '<div style="padding:1em; margin-bottom:1.5em; border:1px solid #EEE; font-size:10px;"><b><font style="color:green;">PUBLISHED</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:blue;">APPROVED</font></b></div>';
	//$specialfoldersHtml = getSpecialfoldersHtml($bizobject);
    $folder = new folder(); 
	$specialfoldersHtml = $folder->getSpecialfoldersHtml($bizobject);
    echo $specialfoldersHtml;
    wcmGUI::closeCollapsablePane();

    echo '</div>';