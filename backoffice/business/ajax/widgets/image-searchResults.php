<?php

// Initialize system
require_once dirname(__FILE__).'/../../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();

// Retrieve current session
$session = wcmSession::getInstance();

$config = wcmConfig::getInstance();

?>
<script type="text/javascript">

loadImageInfo = function(argId)
{
    var w = wcmWidgetContainer.widgets.get('<?php echo $_POST['widgetId']; ?>');
    var wc = w.getWidgetControl('<?php echo $_POST['controlId']; ?>');
    wc.displayImageInfo(argId);
    return false;
}
</script>


<?php

if (isset($_POST['fulltext']))
{
    
    $client = new SoapClient($config['wcm.backOffice.url']."webservices/service.php?class=wcmBusinessSearchWebService&wsdl");
    $token = $_SESSION['ice_webServices_token'];
    
    $id = uniqid();
    
    $class = new stdClass();
    $class->name = 'className';
    $class->value = 'photo';
    
    $type = new stdClass();
    $type->name = 'fulltext';
    $type->value = $_POST['fulltext'];
    
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
    
        $xpath = '/photo/thumbnail';
        $thumbNail = $xp->query($xpath)->item(0)->nodeValue;    
        
        $photos[] = array('id' => $id, 'thumbnail' => $thumbNail);
        
    }
    
}

foreach ($photos as $photo)
{
    ?>
    
<img src="<?php echo $photo['thumbnail']; ?>" alt="<?php echo $photo['id']; ?>" onclick="loadImageInfo('<?php echo $photo['id']; ?>');" />
    
    <?php
}
?>