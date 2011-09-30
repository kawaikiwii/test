    <h4>{'_BIZ_SHARED_MEDIA'|constant}</h4>

    {if $bizobject.photos|@count > 0}
        <ul class="media">
	    {foreach from=$bizobject.photos item=photo key=key}
	        <li>
	            <a href="?_wcmAction=business/photo&id={$photo.id}"><img src="{$photo.thumbnail}" width="{$photo.thumbWidth}" height="{$photo.thumbHeight}" alt="{$photo.title}" title="{$photo.title}" /></a>
	            <p>{$photo.caption|truncate:50:'...'}</p>
	        </li>
	    {/foreach}
        </ul>
    {else}
        {'_BIZ_NO_DETAIL'|constant}
    {/if}

