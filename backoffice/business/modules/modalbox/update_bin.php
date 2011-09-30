<?php
/**
 * Project:     WCM
 * File:        modules/modalbox/update_bin.php
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
    if ($id)
    {
        $bizobject->refresh($id);
        echo "<input type=\"hidden\" name=\"idBin\" id=\"idBin\" value=\"".$id."\">";
    }
    ?>
    <fieldset>
        <ul>
            <li>
                <label><?php echo _BIZ_TITLE;?></label>
                <input type="text" id="bin_name" value="<?php echoH8($bizobject->name); ?>" readonly="readonly"/>
            </li>
            <li>
                <label><?php echo _BIZ_DESCRIPTION;?></label>
                <textarea id="bin_description" rows="" cols=""><?php echoH8($bizobject->description); ?></textarea>
            </li>
        </ul>
    </fieldset>
</form>
<ul class="toolbar">
    <li><a href="#" onclick="closemodal(); return false;" class="cancel"><?php echo _BIZ_CANCEL;?></a></li>
    <li><a href= "#" onclick="manageBin('updateBin',document.getElementById('bin_name').value, document.getElementById('bin_description').value, '', document.getElementById('idBin').value, 'bins', false); closemodal(); return false;" class="save"><?php echo _BIZ_SAVE;?></a></li>
</ul>
