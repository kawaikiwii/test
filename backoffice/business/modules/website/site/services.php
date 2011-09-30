<?php
/**
 * Project:     WCM
 * File:        modules/website/site/services.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

    $bizobject = wcmMVC_Action::getContext();

    // Load languages
    $languages = $bizobject->getLanguageList();

    // Render form
    echo '<div class="zone">';
    wcmGUI::openCollapsablePane(_BIZ_SERVICES);
    wcmGUI::openFieldset();
    $classList = getClassList();
    $authorizedServices = explode('|', $bizobject->services);
    ?>
			<table cellspacing="1" cellpadding="3" border="0" bgcolor="#c0c0c0">
			<tr bgcolor="#f4f4f4">
				<td width="40" align="center"> <b> Affich&eacute; </b> </td>
			<td width="140"> &nbsp; <b> <?php echo _BIZ_KIND_ELEMENT; ?> </b> </td>
			</tr>
			<?php
			foreach($classList as $className => $classLabel)
			{
					$checked = '';
					if (in_array($className, $authorizedServices))
						$checked = 'checked';
					
					echo "<tr bgcolor='#ffffff'><td align='center'> <input type='checkbox' name='classList[]' value='$className' $checked> </td>";
					echo "<td> &nbsp; " . $classLabel . "</td></tr>";
			}
			echo "</tr>";    	
			echo "</table>";
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    echo '</div>';