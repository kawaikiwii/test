<div id="featured">
    <h2>Featured</h2>
    {if $owidget->context->id eq 1 }
        {assign var="where" value="id > 0"}
    {else}
        {assign var="where" value="channelId=`$owidget->context->id`"}
    {/if}
    {loop class="article" where=$where orderby="modifiedAt DESC" limit="4"}
        {include file="demo/blocks/article_list.tpl" first=$loop.article.first}
    {/loop}
</div>