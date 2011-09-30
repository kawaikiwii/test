<item>
    <title>{$article.title}</title>
    <link>{$article|@wcm:url}</link>
    <content:encoded>
    <![CDATA[
        {if $article.abstract}
            {$article.abstract|strip_tags|truncate:250:"..."}
        {else}
            {loop class="chapter" of="article" order="rank" limit="1"}
                {$chapter.text|strip_tags|truncate:250:"..."}
            {/loop}
        {/if}
        in <a href="{$article.channel|@wcm:url}">{$article.channel.title}</a>
   ]]>
   </content:encoded>
   <pubDate>{$article.createdAt}</pubDate>
</item>
