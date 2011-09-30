<?php
/**
 * Project:     WCM
 * File:        business/modules/search/filters/abstract.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$options = $params;          // from wcmModule() call

$id           = getArrayParameter($options, 'id', null);
$title        = getArrayParameter($options, 'title', null);
$facets       = getArrayParameter($options, 'facets', null);
$filter     = getArrayParameter($options, 'filter', null);
$searchId     = getArrayParameter($options, 'searchId', null);
$searchEngine = getArrayParameter($options, 'searchEngine', null);
$sort         = getArrayParameter($options, 'sort', true);
//print_r($options); exit();

$buttonId = $id.'_button';
$panelId = $id.'_panel';
$displayStyle = $filter->open ? '' : 'style="display:none"';
?>
<h5>
    <a id="<?php echo $buttonId; ?>" href="#"
       onclick="toggleSearchFilter('<?php echo $id; ?>');">
       <?php echo $title; ?>
    </a>
</h5>
<p id="<?php echo $panelId; ?>" class="selection" <?php echo $displayStyle; ?> >
    <?php
    if ($facets && $searchId && $filter->open)
    {
        $maxValues = 10; // TODO make configurable
        $bizsearch = wcmBizsearch::getInstance($searchEngine);
		//print_r($bizsearch);
        $values = $bizsearch->getFacetValues($facets, $searchId, $maxValues, $sort);
		
        $checkedValues = array();
        $unckeckedValues = array();

        // TODO keep checked values in $_SESSION since sometimes they
        // disappear from the top-N facet values

        if ($values)
        {
            $checkedItems = $filter->checkedItems;
            foreach ($values as $rawValue => $value)
            {
                if ($value->count > 0)
                {
                    $itemId = $id.'_item__'.preg_replace('/[^A-Za-z0-9_-]/', '_', $value->text);
                    if (isset($checkedItems->$itemId))
                    {
                        $value->checked = true;
                        $checkedValues[] = $value;
                    }
                    else
                    {
                        $value->checked = false;
                        $unckeckedValues[] = $value;
                    }
                }
            }
        }

        if ($checkedValues || $unckeckedValues)
        {
            foreach (array_merge($checkedValues, $unckeckedValues) as $value)
            {
                $itemId = $id.'_item__'.preg_replace('/[^A-Za-z0-9_-]/', '_', $value->text);
                $checked = $value->checked ? 'checked="checked"' : '';
                $rawValue = addslashes(wcmBizsearch::escapeQuery($value->value));
                ?>
                <span>
                    <input type="checkbox" id="<?php echo $itemId; ?>" <?php echo $checked; ?>
                           onclick="refineSearch('<?php echo $id; ?>',
                                                 '<?php echo $itemId; ?>',
                                                 this.checked,
                                                 '<?php echo $value->searchIndex; ?>',
                                                 '<?php echo $rawValue; ?>')" />
                    <a href="#" title="<?php echo $value->value; ?>"
                       onclick="refineSearch('<?php echo $id; ?>',
                                             '<?php echo $itemId; ?>',
                                             !$('<?php echo $itemId; ?>').checked,
                                             '<?php echo $value->searchIndex; ?>',
                                             '<?php echo $rawValue; ?>')">
                        <?php echo getConst($value->text) ; ?> (<?php echo $value->count; ?>)
                    </a>
                </span>
                <br />
                <?php
            }
        }
    }
    ?>
</p>
