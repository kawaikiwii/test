<?php
/**
 * Project:     WCM
 * File:        modules/modalbox/export_bin.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$id = $params[0];
?>
<form onsubmit="return false;">
    <?php
    $project = wcmProject::getInstance();
    $bizobject = new wcmBin($project);
	$config = wcmConfig::getInstance();
    $bo_url = $config['wcm.backOffice.url'];
    $fo_url = $config['wcm.exportsPaniers.path'];
    
	$templatesPage = array_flip($project->generator->getTemplatesByPath('exports/'));

	if ($id)
    {
        $bizobject->refresh($id);
        echo "<input type=\"hidden\" name=\"idBin\" id=\"idBin\" value=\"".$id."\">";
        
        if (!empty($bizobject->content))
        {
    ?>
    <fieldset>
        <ul>
            <li>
                <nobr><label><?php echo _BIZ_SELECTED_BIN;?> : <?php echoH8($bizobject->name); ?></label></nobr> 
            </li>
            <li>
               <nobr>(<span style='font-size : smaller;'><?php echo $fo_url."/".$bizobject->name;?></span>)</nobr><br />
            </li>
            <li>&nbsp;</li>
            <li>
               <?php
               wcmGUI::openFieldset();
			   wcmGUI::renderDropdownField('template_choice', $templatesPage, '', _BIZ_TEMPLATE, array('id' => 'template_choice'));
			   wcmGUI::closeFieldset(); 
			   ?>
            </li>
        </ul>
    </fieldset>
    <br />
</form>
<ul class="toolbar">
    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
    <li><a href= "#" onclick="window.open('<?php echo $bo_url;?>business/popup/export_panier.php?binid=<?php echo $id;?>&template='+$('template_choice').value,'exportbin','resizable=disallow,scrollbars=1,location=0,status=1,toolbar=0,width=800,height=400'); closemodal(); return false;" class="save"><?php echo _BIZ_APPROVE;?></a></li>
</ul>
<?php
        }
        else 
        {
        	?>
        	<fieldset>
	        <ul>
	            <li>
	                <nobr><label><?php echo _BIZ_EMPTY_BIN;?>....</label></nobr> 
	            </li>
	            </ul>
    		</fieldset>
    		<ul class="toolbar">
			    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
			</ul>
        	<?php  
        }
    }
