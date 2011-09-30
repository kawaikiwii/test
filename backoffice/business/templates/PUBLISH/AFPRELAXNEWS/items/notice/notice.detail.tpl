
<div id="preview-{$className}_{$notice.id}" class="ari-preview ari-{$className}{if $medias|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$notice.mainChannelCss}">{$notice.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$notice.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
{if $medias|@count gt 0}
{foreach from=$notice.relateds item=related}
{assign var=widthAndHeight value=$related.object->getWidthAndHeight('w400')}
				<div class="ari-illustration photo">
					<div class="ari-illustration-item"> 
						<img src="{$related.object->getPhotoUrlByFormat('w400')}" width="{$widthAndHeight.width}" height="{$widthAndHeight.height}" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
					</div>
					<div class="ari-illustration-detail">
						<h3 class="ari-illustration-legend">{$related.relation.title}</h3>
						{if $related.relation.media_description}<h3 class="ari-illustration-legend" style="font-size:9px;">{$related.relation.media_description}</h3>{/if}
						<h3 class="ari-illustration-rights">©{$related.object->credits}</h3>
{if $related.object->specialUses}
							<table cellpadding=0 cellspacing=0 style="padding:7px; text-align:left;">
								<tr><td style="padding-top:7px;"><b>Special Uses</b></td></tr>
								<tr><td>{$related.object->specialUses}</td></tr>
							</table>
{/if}
					</div>
				</div>
{/foreach}
{else}
{assign var="cheminDeFer" value="ok"}
			<div class="ari-illustration photo">
				<div class="ari-illustration-item"> 
					<div style="width:220px; height:220px;">&nbsp;</div>
				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-legend">&nbsp;</h3>
				</div>
			</div>
{/if}
	</div>
	<div class="ari-content">
		<span class="ari-publishDate">{$notice.publicationDateFormatted.displayFormat}</span>
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-slugline">{foreach from=$notice.slugLine item=slug name=foo}{if $slug.type == 'channel'}
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
{if $notice.sourceLocation != ""}{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$notice.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}{/if}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$notice.sourceLabel`</span>)"}	
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