<font size="2">
    {$prevision.sourceLabel} | {$prevision.publicationDateFormatted.displayFormat}
</font>
<h1 style="margin-top:0;">{$content.title}</h1>
	<div style="float:left;width:250px;padding:10px;">
{foreach from=$prevision.relateds item=related}		
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
{assign var='checkorga' value='0'}	
{foreach from=$prevision.relateds item=related}		
{if $related.relation.destinationClass == 'contact' && $checkorga=='0'}
	{if $related.object->title}<b>Contact Presse:</b> {$related.object->title}{/if}
	{if $related.object->address}<b>Adresse:</b> {$related.object->address}{/if}
	{if $related.object->phone}<b>Tel:</b> {$related.object->phone}{/if}
	{if $related.object->email}<b>Email:</b> <a href="mailto:{$related.object->email}">{$related.object->email}</a>{/if}
	{if $related.object->website}<b>Site internet:</b> <a href="{if $related.object->website|strstr:':'}{$related.object->website}{else}http://{$related.object->website}{/if}" target="_blank">{$related.object->website}</a>{/if}
	{if $related.object->facebook}<b>Facebook:</b> {$related.object->facebook}{/if}
{assign var='checkorga' value='1'}
{/if}
{/foreach}
</div>
