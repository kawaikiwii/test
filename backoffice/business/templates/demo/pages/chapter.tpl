{load class="site" where="id=`$article.siteId`"}
{load class="channel" where="id=`$article.channelId`"}
{include file="demo/blocks/header.tpl" bizobject=$article}
{include file="demo/blocks/ugc/hit_counter.tpl" bizobject=$article}
<div id="body">
    <div id="content">

        <div id="toolbox">
            {include file="demo/blocks/ugc/toolbox.tpl" bizobject=$article}
            {include file="demo/blocks/ugc/rating.tpl" bizobject=$article}
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
                <img src="{$photo|wcm:photoUrl}"
                     width="200"
                     alt="{$photo.title}" title="{$photo.title}" />
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
            {$chapter.text}
            <div id="chapter_pages">
                <ol>
                {loop name="links" class="chapter" of="article" orderby="rank"}
                    <li>
                        Page {$loop.links.iteration} - 
                        {if $chapter.id eq $links.id}
                            {* Use title of article if chapter has no title defined *}
                            {if $chapter.title eq ''}
                                {$article.title}
                            {else}
                                {$chapter.title}
                            {/if}
                        {else}
                            {* Use title of chapter if chapter has no title defined *}
                            {if $links.title eq ''}
                                <a href="{$links|@wcm:url}">{$article.title}</a>
                            {else}
                                <a href="{$links|@wcm:url}">{$links.title}</a>
                            {/if}
                        {/if}
                    </li>
                {/loop}
                </ol>
            </div>

            {include file="demo/blocks/ugc/comments.tpl" article=$article}
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
{include file="demo/blocks/footer.tpl"}
