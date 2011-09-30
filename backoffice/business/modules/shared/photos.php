<?php
    // Change context to accept picture
    wcmMVC_Action::execute('business/photo', array('class' => 'photo'));
    $bizobject = wcmMVC_Action::getContext();

	wcmGUI::openForm("createPhoto");
    $actions = '<ul class="actions">'
             . '<li><a href="#" onclick="openmodal(\'' . _BIZ_UPLOAD_PHOTO . '\'); modalPopup(\'changephoto\',\'new\', \'\'); return false;">' . _BIZ_UPLOAD_PHOTO . '</a></li>'
             . '</ul>';
    wcmGUI::openCollapsablePane(_BIZ_IMAGE, true, $actions);

    wcmGUI::openFieldset(_BIZ_ORIGINAL);
    echo '<li><label>' . _BIZ_FILE . '</label><span>' . $bizobject->original . '</span></li>';
    echo '<li><label>' . _BIZ_DIMENSIONS . '</label>';
    echo '<span id="originalWidth"> ' . $bizobject->width . '</span> x ';
    echo '<span id="originalHeight">' . $bizobject->height , '</span> pixels</li>';
    wcmGUI::renderHiddenField('original', $bizobject->original, array('id' => 'original'));    wcmGUI::renderHiddenField('width', $bizobject->height, array('id' => 'width'));
    wcmGUI::renderHiddenField('height', $bizobject->width, array('id' => 'height'));
    wcmGUI::closeFieldset();
    wcmGUI::closeCollapsablePane();
    wcmGUI::openCollapsablePane(_BIZ_MANAGE_CROPPING);

    // Make sure we use a default image
    if (!$bizobject->original) $bizobject->original = "img/none.gif";
    $cmanager = new croppingManager($bizobject, WCM_DIR . '/business/xml/photosratios/default.xml');
    ?>
    <div id="cropping">
        <ul id="cropping_ratios">
        <?php
            foreach($cmanager->ratios as $key => $value)
            {
                echo '<li>';
                echo '<a class="ratio" href="#" alt="' . $key . '"';
                echo ' onclick="CropImageManager.select(this, \'ratio' . $value['rx'] . 'x' . $value['ry']. '\'); return false;"/>';
                echo getConst($key) . '</a>';
                echo '</li>';
            }
        ?>
        </ul>
        <div id="cropped_infos">
            <span id="cropped_coords"></span>
            <img class="clear" onclick="CropImageManager.clear();"/>
            <img class="save" onclick="CropImageManager.set();"/>
            <img class="undo" onclick="CropImageManager.undo();"/>
        </div>
        <div id="cropped_image"></div>
        <div id="cropping_ratios"><?php $cmanager->generateHiddenFields(); ?></div>
    </div>
<?php

  $actions = '<ul class="actions">'
             . '<li><a href="#" onclick="bizCreatePhoto(\'_wcm_rel_photos_relations\');">' . _BIZ_SAVE . '</a></li>'
			 . '</ul>';
  	
			 
    wcmGUI::closeCollapsablePane();

    wcmGUI::openCollapsablePane(_BIZ_CONTENT, true, $actions);
	
    wcmGUI::openFieldset('', array('id' => 'pageFieldsetPhotoCredits'));
    wcmGUI::renderTextField('credits', '', _BIZ_CREDITS .' *',  array('class' => 'type-req'));    
	wcmGUI::renderTextField('keywords', '', _BIZ_KEYWORDS);
    wcmGUI::closeFieldset();	
	
    wcmModule('business/shared/content', array('bizObjectClass' => $bizobject->getClass()));
    wcmGUI::closeCollapsablePane();	
	wcmGUI::closeForm();
