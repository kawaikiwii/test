{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}

<div class="row">
    
    <div class="toolbar">
        {* include the toolbar *}
        {wcm name="include_template" file="search/views/detailed/toolbar.tpl"}
    </div>

    <div class="details {$objectClass}">
        <ul>
            <li class="detail title">{$object->title}</li>
            <li class="detail"><span class="label">{'_BIZ_DESCRIPTION'|constant}</span> {$object->description}</li>

			{assign var="photos" value=$object->getPhotos()}
			{assign var="maxImages" value=12}
            {assign var="postfix" value='_BIZ_PHOTO'|constant}
            {* Add the 's' at the end of the postifx if necessary *}
            {if $photos|@count > 1}
                {assign var="postfix" value="`$postfix`s"}
            {/if}

            <li class="detail">{$photos|@count} {$postfix}</li>
            <li class="detail">
                <ul>
                    {foreach name="loop" item="photo" from="$photos"}
                        {if $smarty.foreach.loop.index < $maxImages} 
                        <li class="slideshow-image">
                            <img src="{$photo->thumbnail}" ></img>
                        </li>
                        {elseif $smarty.foreach.loop.index == $maxImages}
                        <li class="slideshow-image">[...]</li>
                        {else}
                            {* Do nothing... *}
                        {/if}
                    
                    {/foreach}
                </ul>   
            </li>
            
            {wcm name="include_template" file="search/views/detailed/details/comments.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/permalinks.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/iptcCategories.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tags.tpl"}
            {wcm name="include_template" file="search/views/detailed/details/tme.tpl"}

        </ul>
    </div>
    
    <div class="metadata">
        {* include generic metadata *}
        {wcm name="include_template" file="search/views/detailed/metadata.tpl"}
    </div>
</div>
