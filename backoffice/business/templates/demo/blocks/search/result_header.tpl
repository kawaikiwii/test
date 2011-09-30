<div id="search_result_header">
    <p class="search_result_page_info">
    <?php
        if ($searchResult->pageMax > 0)
            echo sprintf('Showing %s to %s of %s results', $searchResult->first, $searchResult->last, $searchResult->numFound);
        else
            echo 'No results';
    ?>
    </p>
    <ul class="search_result_page_links">
        <?php
        foreach ($searchResult->pages as $page)
            echo '<li class="search_result_page_link">' . $searcher->renderPageLink($page) . '</li>';
        ?>
    </ul>
</div>
