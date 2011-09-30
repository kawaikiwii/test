<div id="search_result_list">
    <ul class="search_result_items">
    <?php
        foreach ($searchResult->items as $item)
        {
            echo '<li class="search_result_item">';
            echo $searcher->renderResultItem($item, 'demo/blocks/search/result_item.tpl');
            echo '</li>';
        }
    ?>
    </ul>
</div>
