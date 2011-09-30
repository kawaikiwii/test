{load class="site" where="id=`$context.site`"}{include file="demo/blocks/search/header.tpl"}
<div id="wrapper">
    <div id="search">
        <h1>Site Search</h1>
        {include file="demo/blocks/search/query.tpl"}
        {include file="demo/blocks/search/result.tpl"}
        {include file="demo/blocks/search/filters.tpl"}
    </div>
</div>
{include file="demo/blocks/search/footer.tpl"}
