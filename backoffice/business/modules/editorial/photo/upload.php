<?php
include(WCM_DIR.'/initWebApp.php');
include(WCM_DIR.'/pages/includes/head.php');
$config = wcmConfig::getInstance();
$command = array_shift($params);

if (isset($_FILES['photo']))
{
    switch ($_FILES['photo']['error'])
    {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errMsg = sprintf(_BIZ_ERROR_UPLOAD_SIZE, (isset($_POST['MAX_FILE_SIZE']))? $_POST['MAX_FILE_SIZE'] : ini_get('upload_max_filesize'));
            break;
        case UPLOAD_ERR_PARTIAL:
            $errMsg = _BIZ_ERROR_UPLOAD_PARTIAL;
            break;
        case UPLOAD_ERR_NO_FILE:
            $errMsg = _BIZ_ERROR_UPLOAD_NO_FILE;
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $errMsg = _BIZ_ERROR_UPLOAD_NO_TMP_DIR;
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $errMsg = _BIZ_ERROR_UPLOAD_CANT_WRITE;
            break;
        case UPLOAD_ERR_EXTENSION:
            $errMsg = _BIZ_ERROR_UPLOAD_EXTENSION;
            break;
        default:
            $errMsg = null;
    }

    if (!isset($errMsg))
    {
        $uploadedFile = true;
        switch ($command)
        {
            case 'processUpload':

				require_once(WCM_DIR . '/business/api/toolbox/biz.relax.toolbox.php');

				$tempName = '_tmp_' . photo::cleanFileName($_FILES['photo']['name']);

				$ext = strtolower(substr($tempName, strrpos($tempName, '.') + 1));
                if($ext == "jpg" || $ext == "jpeg")// || $ext == "gif" || $ext == "bmp" || $ext == "png")
                {
					$tempFile = $tempName;

					$creationDate = dateOptionsProvider::fieldDateToArray(date('Y-m-d H:i:s'));
					$dir = $config['wcm.webSite.repository'].'illustration/photo/'.$creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/';

					if (checkDirsAndCreateThem($dir, 0777))
					{
						move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $tempFile);
                		list($width, $height) = getimagesize($dir . $tempFile);
					}
                }
                else
                {
                	$uploadedFile = false;
                	echo _BIZ_ERROR_PHOTO_FORMAT;
              		//echo '<script>parent.wcmMessage.error("\$I18N._BIZ_ERROR_PHOTO_FORMAT");</script>';
                }
                break;
            case 'savePhoto':
                break;
        }
    }
}
$uploadAction = wcmModuleURL('business/editorial/photo/upload', array('command' => 'processUpload'));
$saveAction   = wcmModuleURL('business/editorial/photo/upload', array('command' => 'savePhoto'));
?>
<body>
<script type="text/javascript">
startUpload = function()
{
    //parent.iFrameCover($I18N.LOADING);
    if ($('photoField').value != '')
    {
        $('uploadPhotoForm').submit();
    }
}

toggleUploadButton = function()
{
    if ($('photoField').value == '')
    {
        Form.Element.disable('_photo_uploadButton');
    } else {
        Form.Element.enable('_photo_uploadButton');
    }
}

savePhoto = function()
{
    <?php if (isset($tempFile)){ ?>
    parent.$('original').value = '<?php echo $tempName; ?>';
    //parent.$('width').value = '<?php echo $width; ?>';
    //parent.$('height').value = '<?php echo $height; ?>';
    parent.CropImageManager.init(wcmBaseURL + '<?php echo $tempFile; ?>');

    $('savePhotoForm').submit();

    parent.closemodal();
    <?php } ?>
}

</script>
<div id="subform">
<?php
if (isset($uploadedFile) && $uploadedFile)
{
    // File was uploaded, so display some extra stuff
    ?>
    <form action="<?php echo $saveAction; ?>" method="post" id="savePhotoForm">
    <?php
    wcmGUI::openFieldset(_BIZ_PHOTO);
    wcmGUI::renderHiddenField('_wcmPath', $tempFile);
    //wcmGUI::renderHiddenField('_wcmWidth', $width);
    //wcmGUI::renderHiddenField('_wcmHeight', $height);
    ?>
    <img src="<?php echo $config['wcm.webSite.urlRepository'] . 'illustration/photo/' . $creationDate['year'].'/'.$creationDate['month'].'/'.$creationDate['day'].'/' . $tempFile;?>" width="100" />
    <p><?php echo _BIZ_DIMENSIONS ?>: <?php echo $width; ?> x <?php echo $height; ?></p>
    <li>
        <label></label> <button type="button" onclick="window.location=window.location"><?php echo _CANCEL;?></button>
    </li>
    <li>
        <label></label> <button type="button" onclick="savePhoto()"><?php echo _SAVE;?></button>
    </li>
    <?php
    wcmGUI::closeFieldset();
    echo '</form>';
}
else
{
?>
<form action="<?php echo $uploadAction; ?>" enctype="multipart/form-data" method="post" id="uploadPhotoForm">

<?php if (isset($errMsg)): ?>
<p class="error">
<?php echo $errMsg; ?>
</p>
<?php endif; ?>

<ul>
    <li>
        <label><?php echo _BIZ_PICTURE;?></label> <input type="file" name="photo" id="photoField" onchange="toggleUploadButton()" />
    </li>
    <li>
        <input type="button" id="_photo_uploadButton" disabled="disabled" onclick="startUpload()" value="<?php echo _BIZ_UPLOAD_PHOTO; ?>">
    </li>
</ul>
</form>
<?php
}

?>
</div>
</body>
</head>
