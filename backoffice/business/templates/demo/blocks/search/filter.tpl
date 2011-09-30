<div id="search_filter_{$filterName}">
    <h5>{$filterTitle}</h5>
    <ul class="search_filter">
         <?php
         $facetName = '{$filterName}';
         if (isset($searchResult->facetValues->$facetName))
             foreach ($searchResult->facetValues->$facetName as $facetValue)
                 echo '<li class="search_filter_item">'
                     . sprintf('<a href="%s" title="%s">%s</a>',
                               $facetValue->url, $facetValue->urlTitle, $facetValue->label)
                     . '</li>';
         ?>
    </ul>
</div>
