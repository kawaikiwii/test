<div class="post" id="list-{$className}-{$news.id}">
    <div class="post-channel {$news.categorization.mainChannel_css}">
        {$news.categorization.mainChannel_title}
    </div>
    <div class="wptouch-post-thumb-wrap">
        <div class="thumb-top-left ">
        </div>
        <div class="thumb-top-right">
        </div>
        <div class="wptouch-post-thumb">
{if $news.properties.illustration|@count gt 0}
{foreach from=$news.properties.illustration item=illustration name=foo}
            {if $smarty.foreach.foo.index == 0}<img width="50" height="50" src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.thumbnail}" class="attachment-post-thumbnail wp-post-image" alt="{$illustration.legend|trim|htmlspecialchars}" title="{$illustration.legend|trim|htmlspecialchars} />
			{/if}
{/foreach}
{/if}
        </div>
        <div class="thumb-bottom-left">
        </div>
        <div class="thumb-bottom-right">
        </div>
    </div>
    <a class="h2" href="#">{$content.title}</a>
    <div class="post-author">
        {$news.publicationDate|date_format:"%d/%m/%Y à %H:%M"}
    </div>
    <div class="clearer">
    </div>
    <div id="entry-41525" class="mainentry  full-justified">
{assign var=description value=$content.description|strip_tags}
{assign var=description value=$description|truncate:150:" ..."}
        <p>
            {$description}
        </p>
        <a class="read-more" href="#">Read This Post</a>
    </div>
</div>
