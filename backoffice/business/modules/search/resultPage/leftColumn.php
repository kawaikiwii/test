<?php
/**
 * Project:     WCM
 * File:        business/modules/search/leftColumn.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

$context = $params[0];          // from wcmModule() call
$filters = $context->filters;

?>
<div id="filters">
    <h3><?php echo _BIZ_FILTERS ?></h3>
    <ul>
        <li><a href="#" onclick="searchUsingFilters()"><?php echo _BIZ_START_NEW_SEARCH; ?></a> <?php echo _BIZ_WITH_SELECTED_FILTERS; ?></li>
        <li><a href="#" onclick="resetFiltersAndSearch()"><?php echo _BIZ_RESET; ?></a> <?php echo _BIZ_FILTER_SELECTION; ?></li>
    </ul>
    <?php
    $filterOptions = array();
    $filterOptions['searchId'] = (isset($context->id)) ? $context->id : null;
    $filterOptions['searchEngine'] = $context->engine;

    foreach (array('meta_', 'stats_') as $filterType)
    {
        echo '<h4>';
        echo getConst($filterType == 'meta_' ? _BIZ_METADATA : _BIZ_STATISTICS);
        echo '</h4>';

        if ($filters)
        {
            foreach ($filters as $filterName => $filter)
            {
                if (substr($filterName, 0, strlen($filterType)) == $filterType)
                {
                    $filterOptions['filter'] = $filter;
                    echo '<div id="'.$filterName.'">';
                    wcmModule('business/search/filters/' . $filterName, $filterOptions);
                    echo '</div>';
                }
            }
        }
    }
    ?>
</div>
