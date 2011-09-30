<?php
/**
 * File:        /business/modules/editorial/folders/sites.php
 *
 * @copyright   (c)2011 relaxnews
 * @version     4.x
 * @author cc
 *
 */

    $bizobject = wcmMVC_Action::getContext();
    $config = wcmConfig::getInstance();
    $session = wcmSession::getInstance();
	$currentSite = $session->getSite();
	
    // Render form
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_BIZ_SITES);
    wcmGUI::openFieldset();
    
    $site = new site();
    // get all site with name and id informations
    $siteList = $site->getArrayNamesSites();
    // check existing permissions
    $permSites = $bizobject->getSitePermissions();   
    ?>
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#c0c0c0">
			<tr bgcolor="#f4f4f4">
				<td width="40" align="center"> <b> <?php echo _BIZ_ALLOW; ?> </b> </td>
			<td width="250"> &nbsp; <b> <?php echo _BIZ_SITES; ?> </b> </td>
			</tr>
			<?php
			foreach($siteList as $name => $site)
			{
					if ($site != $currentSite->id)
					{
						$checked = '';
						if (in_array($site, $permSites))	$checked = 'checked';
										
						echo "<tr bgcolor='#ffffff'><td align='center'> <input type='checkbox' name='siteList[]' value='$site' $checked> </td>";
						echo "<td> &nbsp; " . $name . "</td></tr>";
					}
			}
			echo "</tr>";    	
			echo "</table>";
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';
