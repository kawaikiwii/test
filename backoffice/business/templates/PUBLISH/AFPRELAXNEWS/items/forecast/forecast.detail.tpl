
<div id="preview-{$className}_{$forecast.id}" class="ari-preview ari-{$className}{if $forecast.properties.illustration|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$forecast.mainChannelCss}">{$forecast.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$forecast.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
{assign var='checkphoto' value='0'}	
{foreach from=$forecast.relateds item=related}
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
{elseif $related.relation.destinationClass == 'work'}
{foreach from=$related.object->getAssoc_relateds() item=related2}
{if $related2.relation.destinationClass == 'photo' && $checkphoto=='0'}
				<div class="ari-illustration photo">
					<div class="ari-illustration-item"> 
						<img src="{$related2.object->getPhotoUrlByFormat('w400')}" alt="{$related2.relation.title|trim}" title="{$related2.relation.title|trim}" style="width:400px"/>
					</div>
					<div class="ari-illustration-detail">
						{if $related2.relation.title != ""}<h3 class="ari-illustration-legend">{$related2.relation.title|trim}</h3>{/if}
						{if $related2.object->credits != ""}<h3 class="ari-illustration-rights">©{$related2.object->credits|trim}</h3>{/if}		
					</div>
				</div>
{assign var='checkphoto' value='1'}	
{/if}
{/foreach}
{/if}
{/foreach}
	</div>
	<div class="ari-content">
		<span class="ari-publishDate">{$forecast.startDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>
{if $forecast.startDate != $forecast.endDate && $forecast.endDate != ''}
			- <span class="ari-publishDate">{$forecast.endDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>			
{/if}
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-slugline">{foreach from=$forecast.slugLine item=slug name=foo}{if $slug.type == 'channel'}
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
{if $forecast.sourceLocation != ""}{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$forecast.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}{/if}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$forecast.sourceLabel`</span>)"}	
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
{foreach from=$forecast.relateds item=related}		
{if $related.relation.destinationClass == 'work' && $related.object->type != "product"}
				 {*<u>{$related.object->title}</u><br />
				 <i>{$related.object->type}</i><br />*}<br />
{foreach from=$related.object->getSpecificInfos() key=label item=info}
{if $info !=""}
		        		<b>{$related.object->getSpecificInfosTrad($label)}</b>: {$info}<br/>
{/if}
{/foreach} 		
{/if}
{/foreach}
{* CONTACTS *}
{assign var='checkorga' value='0'}	
{foreach from=$forecast.relateds item=related}		
{if $related.relation.destinationClass == 'organisation' && $checkorga=='0'}
{*if $related.relation.destinationClass == 'organisation' && $smarty.foreach.foo.first*}
				<br /><br />
				<b>Contact Presse</b><br /><br />
				{if $related.object->name}<b>Nom:</b> {$related.object->name}<br/>{/if}
				{if $related.object->nationality}<b>Nationalité:</b> {$related.object->nationality}<br/>{/if}
				{if $related.object->address_1}<b>Adresse:</b> {$related.object->address_1}<br/>{/if}
				{if $related.object->address_2}<b>Adresse(suite):</b> {$related.object->address_2}<br/>{/if}
				{if $related.object->zipcode}<b>Code Postal:</b> {$related.object->zipcode}<br/>{/if}
				{if $related.object->country}<b>Pays:</b> {$related.object->country}<br/>{/if}
				{if $related.object->phone}<b>Tel:</b> {$related.object->phone}<br/>{/if}
				{if $related.object->fax}<b>Fax:</b> {$related.object->fax}<br/>{/if}
				{if $related.object->email}<b>Email:</b> <a href="mailto:{$related.object->email}">{$related.object->email}</a><br/>{/if}
				{if $related.object->website}<b>Site internet:</b> <a href="{if $related.object->website|strstr:':'}{$related.object->website}{else}http://{$related.object->website}{/if}" target="_blank">{$related.object->website}</a><br/>{/if}
{assign var='checkorga' value='1'}
{elseif $related.relation.destinationClass == 'work'}
{foreach from=$related.object->getAssoc_relateds() item=related2}
{if $related2.relation.destinationClass == 'organisation' && $checkorga=='0'}
						<br /><br />
						<b>Contact Presse</b><br /><br />
						{if $related2.object->name}<b>Nom:</b> {$related2.object->name}<br/>{/if}
						{if $related2.object->nationality}<b>Nationalité:</b> {$related2.object->nationality}<br/>{/if}
						{if $related2.object->address_1}<b>Adresse:</b> {$related2.object->address_1}<br/>{/if}
						{if $related2.object->address_2}<b>Adresse(suite):</b> {$related2.object->address_2}<br/>{/if}
						{if $related2.object->zipcode}<b>Code Postal:</b> {$related2.object->zipcode}<br/>{/if}
						{if $related2.object->country}<b>Pays:</b> {$related2.object->country}<br/>{/if}
						{if $related2.object->phone}<b>Tel:</b> {$related2.object->phone}<br/>{/if}
						{if $related2.object->fax}<b>Fax:</b> {$related2.object->fax}<br/>{/if}
						{if $related2.object->email}<b>Email:</b> <a href="mailto:{$related2.object->email}">{$related2.object->email}</a><br/>{/if}
						{if $related2.object->website}<b>Site internet:</b> <a href="{if $related2.object->website|strstr:':'}{$related2.object->website}{else}http://{$related2.object->website}{/if}" target="_blank">{$related2.object->website}</a><br/>{/if}
{assign var='checkorga' value='1'}
{/if}
{/foreach}
{/if}
{/foreach}
		</div>
	</div>	
</div>
