<font size="2">
    {$forecast.sourceLabel} | {$forecast.publicationDateFormatted.displayFormat}
</font>
<h1 style="margin-top:0;">{$content.title}</h1>
	<div style="float:left;width:250px;padding:10px;">
{foreach from=$forecast.relateds item=related}		
{if $related.relation.destinationClass == 'work'}
{foreach from=$related.object->getAssoc_relateds() item=related2}
{if $related2.relation.destinationClass == 'photo'}
					<div>
						<img src="{$related2.object->getPhotoUrlByFormat('w250')}" alt="{$related2.relation.title|trim|htmlspecialchars}" title="{$related2.relation.title|trim|htmlspecialchars}" style="width:250px"/>
						{if $related2.object->rights != ""}<h5>©{$related2.object->rights|trim}</h5>{/if}
					</div>			
{/if}
{/foreach}
{/if}
{/foreach}
	</div>
<div style="margin:10px;">
    {$content.description}
    {$content.text}
</div>
