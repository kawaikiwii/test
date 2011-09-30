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

searchImages = function()
{

    var controlId = '<?php echo $_POST['controlId']; ?>';
    var widgetId = '<?php echo $_POST['widgetId']; ?>';
    
    var wc = wcmWidgetContainer.widgets.get(widgetId).getWidgetControl(controlId);
    wc.searchImages();

    return false;
}
</script>           
    
    
<div class="imageSearchBar">
<input type="text" name="fulltext" id="fulltext_<?php echo $_POST['controlId']; ?>"> <button onclick="searchImages(); return false;">SEARCH</button>
</div>
<div id="imageResults_<?php echo $_POST['controlId']; ?>" class="imageSearchResults">
</div>
