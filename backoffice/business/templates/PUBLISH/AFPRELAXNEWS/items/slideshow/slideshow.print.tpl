
<table>
<tr><td valign="top">
	<font size="2">{$slideshow.sourceLabel} | {$slideshow.publicationDateFormatted.displayFormat}</font>
	<h1 style="margin-top:0;">{$content.title}</h1>
	<table><tr>
{if $medias|@count gt 0}
			<td valign="top" width="300">
{foreach from=$slideshow.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
					<table border="1" width="100%"><tr><td valign="top">
						<img src="{$related.object->getPhotoUrlByFormat('w400')}" width="400" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
					</td></tr><tr><td valign="top">
						©{$related.object->credits}<br>
						<b>{$related.relation.title}</b>
						{if $related.relation.media_description}<br>{$related.relation.media_description}{/if}
						{if $related.relation.media_link}<br>{$related.relation.media_link}{/if}
					</td></tr></table>
{/if}
{/foreach}
			</td>
{/if}
		<td valign="top">
			{$content.description}
			{$content.text}
		</td>
</td></tr>
</table>