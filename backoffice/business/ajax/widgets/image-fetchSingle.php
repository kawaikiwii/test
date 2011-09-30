<?php

// Initialize system
require_once dirname(__FILE__).'/../../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve current session
$session = wcmSession::getInstance();

$config = wcmConfig::getInstance();

$client = new SoapClient($config['wcm.backOffice.url']."webservices/service.php?class=wcmBusinessSearchWebService&wsdl");
$token = $_SESSION['ice_webServices_token'];
$id = uniqid();

$photoId =  $_POST['photoId'];

$class = new stdClass();
$class->name = 'className';
$class->value = 'photo';

$type = new stdClass();
$type->name = 'id';
$type->value = $photoId;

$totalResults = $client->search($token, $id, array($class,$type));
$results = $client->getSearchResults($token,$id,0,$totalResults-1);

$dom = new DOMDocument('1.0','UTF-16');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;



foreach ($results as $result)
{
    $result = str_replace('\r\n',"",$result);

    $dom->loadXML($result);
    $xp = new DOMXPath($dom);    
    $xpath = '/photo/id';
    
    $id = $xp->query($xpath)->item(0)->nodeValue;
    if ($id == $photoId)
    {
        // This is what we want.
        break;
    } else {
        continue;
    }
}


// Get thumbnail
$xpath = '/photo/thumbnail';
$thumbNail = $xp->query($xpath)->item(0)->nodeValue;

// Get width
$xpath = '/photo/thumbWidth';
$width = $xp->query($xpath)->item(0)->nodeValue;

// get high
$xpath = '/photo/thumbHeight';
$height = $xp->query($xpath)->item(0)->nodeValue;

// get date
$xpath = '/photo/createdAt';
$createdAt = $xp->query($xpath)->item(0)->nodeValue;

$xpath = '/photo/modifiedAt';
$modifiedAt = $xp->query($xpath)->item(0)->nodeValue;

$xpath = '/photo/caption';
$caption = $xp->query($xpath)->item(0)->nodeValue;
?>
<script type="text/javascript">
useImage = function(argId, argSrc)
{
    w = wcmWidgetContainer.widgets.get('<?php echo $_POST['widgetId']; ?>');
    wc = w.getWidgetControl('<?php echo $_POST['controlId']; ?>');
    wc.useImage(argId, argSrc);
}
</script>
    
    
    
<div class="fetchSingleImage">
    <img src="<?php echo $thumbNail; ?>" alt="<?php echo addslashes(strip_tags($caption)); ?>" style="float: left" />
    <ul>
        <li>Commands: <span class="useImage" onclick="useImage('<?php echo $id; ?>', '<?php echo $thumbNail; ?>')">Use Image</span>
        <li>
            Dimentions: <?php echo $width; ?>px/<?php echo $height; ?>px
        </li>
        <li>
            Created On: <?php echo $createdAt; ?>
        </li>
        <li>
            Modified On: <?php echo $modifiedAt; ?>
        </li>
        <li>
            ID: <?php echo $photoId; ?> (<?php echo $_POST['photoId']; ?>)
        </li>
     </ul>
     <p>
        <?php echo $caption; ?>
     </p>
</div>
