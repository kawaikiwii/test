<?php
/**
 * Project:     WCM
 * File:        business/modules/import/files.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
 
    // retrieve plugin className
    $plugin = getArrayParameter($params, 'plugin');
	
	switch ($plugin)
    {
        case 'wcmImportAFP':
            $xslDir = 'AFP/';
            break;
        case 'wcmImportXML':
        default:
            $xslDir = '';
            break;
    }

    // prepare start button
    $start = '<ul><li><a href="#" onclick="window.open(\'\', \'importLog\', ' .
             '\'resizable=disallow,scrollbars=1,location=0,status=1,toolbar=0,width=800,height=400\');' .
             '$(\''.$plugin.'_importForm\').submit(); return false;">' .
             _START_IMPORT . '</a></li></ul>';
    
    echo '<div class="zone">';
    wcmGUI::openForm($plugin.'_importForm', 'dialogs/import.php', '', array('target'=>'importLog', 'class' => 'mainForm'));
    wcmGUI::renderHiddenField('plugin', $plugin);
    wcmGUI::openCollapsablePane($params['description'], true, $start);

    wcmGUI::openFieldset(_BIZ_IMPORT_FILE_LOCATION);
    wcmGUI::renderTextField($plugin.'_sourceFolder', WCM_DIR.'/business/import/xml/', _BIZ_IMPORT_SOURCE_FOLDER);
    wcmGUI::closeFieldset();

    wcmGUI::openFieldset(_BIZ_IMPORT_TRANSFORMATION_SETTINGS);
    wcmGUI::renderTextField($plugin.'_xslFolder', WCM_DIR.'/business/import/xsl/' . $xslDir, _BIZ_IMPORT_XSL_TEMPLATE_LOCATION);
    wcmGUI::closeFieldset();

    wcmGUI::openFieldset(_BIZ_IMPORT_MEDIA_SETTINGS);
?>

    <nobr><label><?php echo _BIZ_IMPORT_EMBEDDED_PHOTOS; ?></label>
        <input type="radio" name="<?php echo $plugin;?>_embeddedPhotosLocation" value="local" checked="checked" onclick="if (this.checked == true) $('<?php echo $plugin; ?>_embeddedPhotos').disable()" /> <?php echo _SAME_AS_SOURCE_FOLDER; ?>
        <input type="radio" name="<?php echo $plugin;?>_embeddedPhotosLocation" value="remote" onclick="if (this.checked == true) $('<?php echo $plugin; ?>_embeddedPhotos').enable()" /> <?php echo _DIFFERENT_DIRECTORY; ?>
    </nobr><br />
    <nobr><label><?php echo _DATA_SOURCE; ?></label> <input type="radio" name="<?php echo $plugin;?>_mediaData" value="iptc" checked="checked" /> <?php echo _JPEG_IPTC; ?> <input type="radio" name="<?php echo $plugin;?>_mediaData" value="xml" /> <?php echo _XML_DOCUMENTS; ?></nobr><br /><br />  
    <nobr><label><?php echo _BIZ_IMPORT_EMBEDDED_PHOTOS; ?></label> <input type="text" name="<?php echo $plugin; ?>_embeddedPhotos" id="<?php echo $plugin; ?>_embeddedPhotos" size="80" value="<?php echo WCM_DIR; ?>/business/import/media/" disabled="disabled" /></nobr><br />
    </fieldset>
	</ul>
<?php
    wcmGUI::closeForm();
    echo '</div>';