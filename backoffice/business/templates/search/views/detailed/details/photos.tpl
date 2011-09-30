{assign var="photos" value=$object->getAssoc_photos(false) } {if $photos}
	<li class="detail"><span class="label">{'_BIZ_PHOTOS'|constant}</span>
		<ul>
			{foreach from=$photos item=photo}
			<li><img src="{$photo.thumbnail}"></img></li>
			{/foreach}
		</ul>
	</li>
{/if}
