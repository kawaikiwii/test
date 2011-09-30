<div id="articleContainerFor_article-{$article.id}">
{if $first}
    {assign var="photo" value=$article|@wcm:firstPhoto}
    {if $photo}
        <a href="{$article|@wcm:url}">
            <img src="{$photo|wcm:photoUrl}"
                alt="{$photo.title}" title="{$photo.title}" class="wcmPhotoSelector editable big_pic" id="savePhoto-{$article.id}-{$photo.id}" />
        </a>
    {/if}
    <h3 class="main"><a href="{$article|@wcm:url}" class="wcmSingleLineControl editable" id="article-{$article.id}-title">{$article.title}</a></h3>
{else}
    <h3><a href="{$article|@wcm:url}" class="wcmSingleLineControl editable" id="article-{$article.id}-title">{$article.title}</a></h3>
    {assign var="photo" value=$article|@wcm:firstPhoto}
    {if $photo}
        <a href="{$article|@wcm:url}">
                    <img src="{$photo|wcm:photoUrl:thumbnail}"
                        alt="{$photo.title}" title="{$photo.title}" class="wcmPhotoSelector editable" id="savePhoto-{$article.id}-{$photo.id}" />
        </a>
    {/if}
{/if}
<p>
    By <strong>{$article.author}</strong> 
    In <a href="{$article.channel|@wcm:url}/">{$article.channel.title}</a>
</p>
{if $article.abstract}
    <p class="wcmBasicTextEditorControl editable" id="article-{$article.id}-abstract">{$article.abstract|strip_tags|truncate:250:"..."}</p>
{else}
    {loop class="chapter" of="article" order="rank" limit="1"}
        {$chapter.body|strip_tags|truncate:250:"..."}
    {/loop}
{/if}
<p class="info">
    <span class="comment">{$article.contributionCount}</span> 
    <em>
        Posted at {$article.modifiedAt|date_format:"%I:%M %p"}
        on {$article.modifiedAt|date_format:"%d-%m-%Y"}
        (<a href="{$article|@wcm:url}">read</a>)
    </em>
</p>
</div>
