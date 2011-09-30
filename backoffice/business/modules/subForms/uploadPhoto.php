<?php
include(WCM_DIR.'/initWebApp.php');
include(WCM_DIR.'/pages/includes/head.php');
$config = wcmConfig::getInstance();
$session = wcmSession::getInstance();
$command = getArrayParameter($params, 'command', null);
$error = false;

wcmMVC_Action::execute('business/photo', array('class' => 'photo'));
$photo = wcmMVC_Action::getContext();


switch ($command)
{
    case 'processUpload':
		require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');

		if (!empty($_FILES['photo']['name']))
		{
			DEFINE('_THUMB_WIDTH', 150);
			DEFINE('_THUMB_HEIGHT', 100);
			
			$tempName = '_tmp_' . photo::cleanFileName($_FILES['photo']['name']);
	        
			$ext = substr($tempName, strrpos($tempName, '.') + 1);
	        //if($ext == "jpg" || $ext == "jpeg" || $ext == "gif" || $ext == "bmp" || $ext == "png")
	        if($ext == "jpg" || $ext == "jpeg")
	        {
				$tempFile = $tempName;
				
				$creationDate = dateOptionsProvider::fieldDateToArray(date('Y-m-d H:i:s'));
				$dir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';
	        	
				if (checkDirsAndCreateThem($dir))
				{
					move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $tempFile);
	        		list($width, $height) = getimagesize($dir . $tempFile);
				}
	        }
	        else
	        {
	        	$error = true;
	        	echo _BIZ_ERROR_PHOTO_FORMAT;
	        }
		}
        break;
    case 'savePhoto':
		break;
	}

$uploadAction = wcmModuleURL('business/subForms/uploadPhoto',array('command' => 'processUpload', 'uid' => $params['uid']));
$saveAction = wcmModuleURL('business/subForms/uploadPhoto',array('command' => 'savePhoto', 'uid' => $params['uid']));
?>
<script type="text/javascript">

<?php if (isset($params['photoId'])): ?>

lmgr = parent.relationSearch.linkMgr._object.<?php echo $params['uid']; ?>;
lmgr.addRelationManual('<?php echo $params['photoId']; ?>','photo');

// parent.relationSearch.addManualLink('<?php echo $params['uid']; ?>',<?php echo $photo->id ?>,'photo');
<?php endif; ?>

startUpload = function()
{
    parent.iFrameCover($I18N.LOADING);
    $('uploadPhotoForm').submit();
}

savePhoto = function()
{
	var formValidator = new WCM.FormValidator('savePhotoForm');
  	if (!formValidator.checkFields()) {
  		parent.wcmMessage.error($I18N.INVALID_OR_MISSING_FIELDS);
     	return;
   	}
    parent.iFrameCover($I18N.SAVING);
    $('savePhotoForm').submit();
}

//cancel the save process
cancelPhoto = function()
{
	$('savePhotoForm').cancel.value="1";
    parent.iFrameCover($I18N.SAVING);
    $('savePhotoForm').submit();
}

triggerUploadButton = function(newText)
{
	if (newText != '')
	{
		Form.Element.enable('wcm_uploadButton');
    } else {
    	Form.Element.disable('wcm_uploadButton');
    }
}
<?php if ($error): ?>
//parent.wcmMessage.error("<?php echo $error;?>");
<?php endif; ?>
</script>
<div id="subform">
<?php
if (!isset($_FILES['photo']) || $error)
{
        wcmGUI::openFieldset(_BIZ_PHOTO);
?>
<form action="<?php echo $uploadAction; ?>" enctype="multipart/form-data" method="post" id="uploadPhotoForm">
<ul>
    <li>
        <label><?php echo _BIZ_PICTURE;?> (JPEG)</label> <input type="file" name="photo" id="photoField" onchange="triggerUploadButton(this.value)" />
    </li>
    <li>
        <button type="button" onclick="startUpload()" id="wcm_uploadButton" disabled="disabled"><?php echo _BIZ_UPLOAD_PHOTO;?></button>
    </li>
</ul>
</form>
<?php
        wcmGUI::closeFieldset();
}
    if (isset($_FILES['photo']) && !$error)
    {
        // File was uploaded, so display some extra stuff
        wcmGUI::openFieldset(_BIZ_PHOTO);
		wcmGUI::openObjectForm($photo);
        ?>
        <b><?php echo _BIZ_PICTURE;?><b>

	<br><img src="<?php echo $config['wcm.webSite.urlRepository'] . 'illustration/photo/' . $creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/' . $tempFile;?>" width="200px" />
        <br>
	<?php
	$contents = ($photo->id) ? $$photo->getContents() : array(new content());
	foreach ($contents as $content)
    {
		wcmModule('business/shared/contentRelation', array('content' => $content, 'bizObjectClass' => $photo->getClass()));
	}
        wcmGUI::renderHiddenField('cancel',null);
        wcmGUI::renderHiddenField('_wcmClass',$photo->getClass());
        wcmGUI::renderHiddenField('wcm_Todo','Save');
        wcmGUI::renderHiddenField('_redirect', $params['uid']);
        wcmGUI::renderHiddenField('original',$tempFile);
        wcmGUI::renderHiddenField('height',$height);
        wcmGUI::renderHiddenField('width',$width);
	?>
            <input class="btSearch" type="button" onclick="window.location=window.location" value="<?php echo _CANCEL;?>">
            <input class="btSearch" type="button" onclick="wcmActionController.triggerEvent('save',{});" value="<?php echo _SAVE;?>">
        <?php
        wcmGUI::closeFieldset();
        ?>
        <?php
	wcmGUI::closeForm();
    }
?>
</div>
