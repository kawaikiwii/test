

<div id="article-{$article.id}">
    <div id="content">

        <div id="toolbox">
            {include file="demo/blocks/ugc/toolbox.tpl" bizobject=$article}
            {*include file="demo/blocks/ugc/rating.tpl" bizobject=$article*}
        </div>

        <h1 class="hed">{$article.title}</h1>
        <h2 class="dek">{$article.subtitle}</h2>

        <div id="tags">
            {include file="demo/blocks/tme/tags.tpl" bizobject=$article} <br />
            {include file="demo/blocks/tme/categories.tpl" bizobject=$article}
        </div>

        <div class="lede">
            {$article.abstract}
        </div>

        <p id="byline">By <em>{$article.author}</em></p>

        <div id="image_block">
            {assign var="photo" value=$article|@wcm:firstPhoto}
            {if $photo}
                {math equation="newWidth * height / width"
                      height=$photo.height
                      width=$photo.width
                      newWidth=200
                      assign="photoHeight"
                      format="%d"}
                <img src="{$config.wcm.webSite.url}{$photo.original}"
                     width="200" height="{$photoHeight}"
                     alt="{$photo.title}" title="{$photo.title}" class="wcmPhotoSelector editable" id="savePhoto-{$article.id}-{$photo.id}" />
                <div class="image_caption">{$photo.caption} 
                    {if $photo.credits}
                        ({$photo.credits})
                    {/if}
                </div>
            {/if}

            {include file="demo/blocks/tme/related.tpl" bizobject=$article}
            {include file="demo/blocks/tme/similar.tpl" bizobject=$article}
        </div>

        <div id="article_body">
            <h3>{$chapter.title}</h3>
            <div class="wcmFullTextEditorControl editable" id="article-{$articleId}-{$chapter.id}">{$chapter.text}</div>
            <div id="chapter_pages">
                <ol>
                {loop name="links" class="chapter" of="article" orderby="rank"}
                    <li>
                        Page {$loop.links.iteration} - 
                        {if $chapter.id eq $links.id}
                            {$ochapter->title}
                        {else}
                            <a href="{$chapter|@wcm:url}">{$ochapter->title}</a>
                        {/if}
                    </li>
                {/loop}
                </ol>
            </div>
            {include file="demo/blocks/ugc/comments.tpl  bizobject=$article}
        </div>

    </div>

    <div id="promo_space">

        {include file="demo/blocks/ads/default.tpl"}
        {include file="demo/blocks/tme/subject_map.tpl" article=$article}

        {include file="demo/blocks/ugc/most_popular.tpl"}
        {*include file="demo/blocks/ugc/most_discussed.tpl"*}
        {include file="demo/blocks/ugc/most_recent.tpl"}

    </div>

</div>
