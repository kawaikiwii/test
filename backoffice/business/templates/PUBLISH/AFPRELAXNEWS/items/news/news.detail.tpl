{assign var="breaking" value=""}
<div id="preview-{$className}_{$news.id}" class="ari-preview ari-{$className}{if $medias|@count gt 0} ari-illustrated{/if} {$breaking}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$news.mainChannelCss}">{$news.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$news.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
{if $medias|@count gt 0}
{foreach from=$news.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
			<div class="ari-illustration {if $news.embedVideo != 'NULL' && $news.embedVideo != NULL && $news.embedVideo != ""}video{else}photo{/if}">
				<div class="ari-illustration-item"> 
					<img src="{$related.object->getPhotoUrlByFormat('w400')}" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-legend">{$related.relation.title}</h3>
					{if $related.relation.media_description}<h3 class="ari-illustration-legend" style="font-size:9px;">{$related.relation.media_description}</h3>{/if}
					<h3 class="ari-illustration-rights">©{$related.object->credits}</h3>
{if $related.object->specialUses}
						<table cellpadding=0 cellspacing=0 style="padding:7px; text-align:left;">
							<tr><td style="padding-top:7px;"><b>{'_MEDIA_SPECIAL_USES'|constant}</b></td></tr>
							<tr><td>{$related.object->specialUses}</td></tr>
						</table>
{/if}
				</div>
			</div>
{/if}
{/foreach}
{if $news.embedVideo != 'NULL' && $news.embedVideo != NULL && $news.embedVideo != ""}
		<br><br>
		{$news.embedVideo}
		<br>
		<textarea cols="60" rows="5">{$news.embedVideo}</textarea>
{/if}
{else}
{assign var="cheminDeFer" value="ok"}
			<div class="ari-illustration {if $news.embedVideo != 'NULL' && $news.embedVideo != NULL && $news.embedVideo != ""}video{else}photo{/if}">
				<div class="ari-illustration-item">
{if $news.embedVideo != 'NULL' && $news.embedVideo != NULL && $news.embedVideo != ""}
				{$news.embedVideo}
				<br>
				<textarea cols="60" rows="5">{$news.embedVideo}</textarea>
{else}
				<div style="width:220px; height:220px;">&nbsp;</div>
{/if}
				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-legend">&nbsp;</h3>
				</div>
			</div>
{/if}
	</div>
	<div class="ari-content">
		<span class="ari-publishDate">{$news.publicationDateFormatted.displayFormat}</span>
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-slugline">{foreach from=$news.slugLine item=slug name=foo}{if $slug.type == 'channel'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}channelIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'thema'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'target'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'folder'}
			<a href="#" onclick="ARe.folder.open(this,  {$slug.id})">{$slug.title}</a>
{/if}{if !$smarty.foreach.foo.last} - {/if}{/foreach}</h4>
		<div class="ari-text">
{assign var=source value="("}
{if $news.sourceLocation != ""}{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$news.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}{/if}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$news.sourceLabel`</span>)"}	
{assign var=description value=$content.description}{php}
$source = ($this->get_template_vars('source'));
$description = ($this->get_template_vars('description'));
$description = preg_replace("/\<p\>/i", "<p>".$source." - ",$description, 1);
$this->assign('description', $description);
{/php}
			{$description}
			{$content.text}
		</div>
	</div>	
</div>
