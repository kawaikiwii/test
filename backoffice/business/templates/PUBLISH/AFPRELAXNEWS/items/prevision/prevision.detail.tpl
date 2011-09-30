
<div id="preview-{$className}_{$prevision.id}" class="ari-preview ari-{$className}{if $prevision.properties.illustration|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$prevision.mainChannelCss}">{$prevision.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$prevision.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
{assign var='checkphoto' value='0'}	
{foreach from=$prevision.relateds item=related}
{if $related.relation.destinationClass == 'photo' && $checkphoto=='0'}
			<div class="ari-illustration photo">
				<div class="ari-illustration-item"> 
					<img src="{$related.object->getPhotoUrlByFormat('w400')}" alt="{$related.relation.title|trim|htmlspecialchars}" title="{$related.relation.title|trim|htmlspecialchars}" style="width:400px"/>
				</div>
				<div class="ari-illustration-detail">
					{if $related.relation.title != ""}<h3 class="ari-illustration-legend">{$related.relation.title|trim}</h3>{/if}
					{if $related.object->credits != ""}<h3 class="ari-illustration-rights">©{$related.object->credits|trim}</h3>{/if}		
				</div>
			</div>
{assign var='checkphoto' value='1'}	
{/if}
{/foreach}
	</div>
	<div class="ari-content">
		<span class="ari-publishDate">{$prevision.startDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>
{if $prevision.startDate != $prevision.endDate && $prevision.endDate != ''}
			- <span class="ari-publishDate">{$prevision.endDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>			
{/if}
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-slugline">{foreach from=$prevision.slugLine item=slug name=foo}{if $slug.type == 'channel'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}channelIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'thema'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'target'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'folder'}
			<a href="#" onclick="ARe.folder.open(this, {$slug.id})">{$slug.title}</a>
{/if}{if !$smarty.foreach.foo.last} - {/if}{/foreach}</h4>
		<div class="ari-text">
{assign var=source value="("}
{if $prevision.sourceLocation != ""}{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$prevision.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}{/if}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$prevision.sourceLabel`</span>)"}	
{assign var=description value=$content.description}{php}
$source = ($this->get_template_vars('source'));
$description = ($this->get_template_vars('description'));
$description = preg_replace("/\<p\>/i", "<p>".$source." - ",$description, 1);
$this->assign('description', $description);
{/php}
			{$description}
			{$content.text}
		</div>
		<div>
{* CONTACTS *}
{assign var='checkorga' value='0'}	
{foreach from=$prevision.relateds item=related}		
{if $related.relation.destinationClass == 'contact' && $checkorga=='0'}
				<br /><br />
				{if $related.object->title}<b>Contact Presse:</b> {$related.object->title}<br/>{/if}
				{if $related.object->address}<b>Adresse:</b> {$related.object->address}<br/>{/if}
				{if $related.object->phone}<b>Tel:</b> {$related.object->phone}<br/>{/if}
				{if $related.object->email}<b>Email:</b> <a href="mailto:{$related.object->email}">{$related.object->email}</a><br/>{/if}
				{if $related.object->website}<b>Site internet:</b> <a href="{if $related.object->website|strstr:':'}{$related.object->website}{else}http://{$related.object->website}{/if}" target="_blank">{$related.object->website}</a><br/>{/if}
				{if $related.object->facebook}<b>Facebook:</b> {$related.object->facebook}<br/>{/if}
{assign var='checkorga' value='1'}
{/if}
{/foreach}
		</div>
	</div>	
</div>
