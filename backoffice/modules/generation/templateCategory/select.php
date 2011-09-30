<?php
/**
 * Project:     WCM
 * File:        templateCategory/select.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
    $config = wcmConfig::getInstance();
    $templatesPath = $config['wcm.templates.path'];
    $tree = new wcmTree('templateCat', null, null, null, 'tree_template',null, null, WCM_DIR . '/xsl/tree/templateCategory.xsl', '_wcmOnSelect');
    $tree->initFromSession();
    
?>

<div id="folders" style="overflow:auto; height: 155px;">
<ul class="folderSelect">
<?php
echo $tree->renderHTML();
/*
foreach ($folders as $folder)
{
?>
    <li>
        <a href="#" onclick="onSelect('<?php echo $folder; ?>')" class="select"><?php echo _SELECT; ?></a>
        <a href="#" onclick="resetModal('<?php echo $folder; ?>')" class="open"><?php echo _OPEN; ?></a>
        <?php echo $folder ?>
    </li>
<?php
}
*/
?>
</ul>
</div>

<ul class="toolbar">
    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
</ul>
