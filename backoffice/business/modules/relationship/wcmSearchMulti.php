<?php
$config = wcmConfig::getInstance();

$prefix = getArrayParameter($params, 'prefix', '_wcm_rel_');
$pk = getArrayParameter($params, 'kind', wcmBizrelation::IS_RELATED_TO);
$style = getArrayParameter($params, 'resultSetStyle', 'grid');
$pageSize = getArrayParameter($params, 'pageSize', 9);

// retrieve class filter
$classFilter = getArrayParameter($params, 'classFilter', null);

    if (is_array($classFilter))
    {
        if (count($classFilter) > 0)
        {
            $filter = 'className:('.join(' OR ', $classFilter).')';
        } else {
            $filter = null;
        }
    } elseif (!empty($classFilter)) {
        $filter = 'className:'.$classFilter;
    } else {
        $filter = null;
    }
    
// retrieve search plug-in
$pluginFilter = getArrayParameter($params, 'searchEngine', $config['wcm.search.engine']);
$plugins = simplexml_load_file(WCM_DIR.'/xml/searchPlugins.xml');

if (!$pluginFilter)
{
    // No plugin supplied
    $id = $config['wcm.search.engine'];
    $description = array_shift($plugins->xpath('//plugin[@id=\''.$id.'\']'))->description;
    $singlePlugin = $id;
}
elseif (is_array($pluginFilter))
{
    // Plugin filter supplied as an array
    $totalPlugins = count($pluginFilter);
    
    if ($totalPlugins > 1)
    {
        // There there are more than one option in the array
        foreach ($plugins->plugin as $plugin)
        {
            if (!in_array($plugin->id, $pluginFilter)) continue;
            $pluginList["".$plugin->id] = $plugin->name;
        }
    } elseif (!$totalPlugins) {
        
        // The array is blank, so treat it as if $pluginFilter was null to begin with
        $id = $config['wcm.search.engine'];
        $description = array_shift($plugins->xpath('//plugin[@id=\''.$id.'\']'))->description;
        $singlePlugin = $id;
    } else {
        // There is only one option in the array
        $id = array_shift($pluginFilter);
        $description = array_shift($plugins->xpath('//plugin[@id=\''.$id.'\']'))->description;
        $singlePlugin = $id;
    }
        
}
else
{
    // Plugin is a string
    $id = $pluginFilter;
    $nodes = $plugins->xpath('//plugin[@id=\''.$id.'\']');
    $description = array_shift($nodes)->description;
    $singlePlugin = $id;
}    

if (isset($singlePlugin))
{
    echo '<input type="hidden" id="'.$prefix.'engine" value="'.$singlePlugin.'"/>';
} 
else 
{
    echo '<div><select id="'.$prefix.'engine">';
    foreach($pluginList as $id => $name)
    {
        echo '<option value="'.$id.'">'.$name.'</option>';
    }
    echo '</select></div>';
}

?>
<script type="text/javascript">

relationSearch.prepareSearch({
    filter: '<?php echo $filter; ?>',
    idPrefix: '<?php echo $prefix; ?>',
    resultSet: '<?php echo $prefix; ?>resultsetBox',
    style: '<?php echo $style; ?>',
    pk: '_br_<?php echo $pk; ?>',
    uid: '<?php echo $params['uid']; ?>',
    <?php 
    if (isset($params['relclassname']) && !empty($params['relclassname']))
    	echo "relclassname: '".$params['relclassname']."',";
    if (isset($params['relclassid']) && !empty($params['relclassid']))
    	echo "relclassid: '".$params['relclassid']."',";
    ?>
    pageSize: <?php echo $pageSize; ?>
});

<?php echo $prefix;?>_performSearch = function()
{
	var sobjects = $$("INPUT[name='fobjects[]']").findAll(function(el) { return el.checked }).pluck('value');
	if (sobjects.length == 0 ) alert('please select at least one object!');
	else
	{
		$('<?php echo $prefix; ?>sortCtrl').show();
	    $('<?php echo $prefix; ?>sablier').show();
	    
	    relationSearch.search('<?php echo $params['uid']; ?>', $('<?php echo $prefix;?>query').value, {
	        engine: $('<?php echo $prefix; ?>engine').value,
	        filter: 'classname:' + sobjects
	    });
	}
}

</script>

    <div class="query">
     <ul class="queryBar">
     <strong>Objects: </strong> 
        <?php 
        foreach ($classFilter as $item)
		{
			$selected = "";
			if (in_array($item, $classFilter)) $selected = " checked";
			echo "<INPUT TYPE='checkbox' NAME='fobjects[]' VALUE='".$item."' ".$selected." id='fobjects'>".$item." | ";
		}
        ?>
         </ul>
    </div>
    <div class="query">
     <ul class="queryBar">   
        <input type="text" id="<?php echo $prefix;?>query" value=""/>
        <a href="#" onclick="<?php echo $prefix;?>_performSearch(); return false;"><?php echo _SEARCH; ?></a>
       	<?php
       		//wcmGUI::renderButton('suggest', _BIZ_SUGGEST, array('onclick' => 'var data = wcmgetformdata(); if(data != -1) {wcmModal.showAddReplaceCancel(\'' . _BIZ_SUGGEST . '\', {url: wcmBaseURL + \'business/modules/modalbox/tme_find.php\', parameters : {kind:\'_similars\', uid: \'' . $params['uid'] . '\', data: data}}, tmeSimilarsCallback);} return false;', 'class' => 'list-builder nstein'));
       	?>
     </ul>
    </div>
    <div id="<?php echo $prefix; ?>sortCtrl" style="display: none">
        <strong><?php echo _BIZ_ORDER_BY; ?>:</strong> 
        <a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>', 'createdAt DESC')"><img src="img/blue_arrow_down.png" border="0"></a><?php echo ucfirst(_BIZ_DATE); ?><a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>', 'createdAt')"><img src="img/blue_arrow_up.png" border="0"></a> | 
        <a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>','title')"><img src="img/green_arrow_down.png" border="0"></a><?php echo ucfirst(_TITLE); ?><a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>','title DESC')"><img src="img/green_arrow_up.png" border="0"></a>  | 
        <a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>','modifiedAt DESC')"><img src="img/orange_arrow_down.png" border="0"></a><?php echo ucfirst(_BIZ_MODIFIED); ?><a href="javascript:relationSearch.orderBy('<?php echo $params['uid']; ?>','modifiedAt')"><img src="img/orange_arrow_up.png" border="0"></a>
		<span id="<?php echo $prefix; ?>sablier" class="wait" style="display: none;">&nbsp;</span>
    </div>
    
<div id="<?php echo $prefix; ?>resultsetBox">
</div>

