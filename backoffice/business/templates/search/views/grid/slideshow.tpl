{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}
{assign var="photos" value=$object->getPhotos()}
{assign var="maxImages" value=6}


    <div class="preview bundle">

		<h1 class="{$objectClass} info" title="{$object->title}">{$object->title|truncate:50:"..."}</h1>

        {assign var="postfix" value='_BIZ_PHOTO'|constant}
        {* Add the 's' at the end of the postifx if necessary *}
        {if $photos|@count > 1}
        {assign var="postfix" value="`$postfix`s"}
        {/if}
        <ul class="tiles">
        {foreach name="loop" item="photo" from="$photos"}
            
            {if $smarty.foreach.loop.index < $maxImages}
            <li>
                <img src="{$photo->thumbnail}" width="40"/>
            </li>

            {elseif $smarty.foreach.loop.index == $maxImages}
            {assign var="moreDocuments" value="[...]"}
            {else}
            {* Do nothing... *}
            {/if}
    				

        {/foreach}
        </ul>

        <p>{$photos|@count} {$postfix} {$moreDocuments}</p>

    </div>
    
