{assign var="breaking" value=""}
{if in_array('breaking-news', $news.notifications)}
{assign var="breaking" value="ari-breaking-news"}
{/if}
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
							<tr><td style="padding-top:7px;"><b>Special Uses</b></td></tr>
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
		<div class="ari-text">
			({if $news.sourceLocation != ""}<span class="ari-source ari-source-location">{$news.sourceLocation}</span><span class="ari-source ari-separator">-</span>{/if}<span class="ari-source ari-source-name">{$news.sourceLabel}</span>)			
			{$content.description}
			{$content.text}
		</div>
	</div>
</div>
