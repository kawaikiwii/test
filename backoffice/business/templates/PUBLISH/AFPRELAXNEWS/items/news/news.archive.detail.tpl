<div id="preview-{$className}_{$news.id}" class="ari-preview ari-{$className}{if $medias|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$news.mainChannelCss}">{$news.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$news.categorization.mainChannel_title}</span>
	</div>

	<div class="ari-illustrations">
	{if $news.properties.illustration|@count gt 0}
		
		{foreach from=$news.properties.illustration item=illustration}
		{if $illustration.original}
			<div class="ari-illustration photo">
				<div class="ari-illustration-item"> 
					<img src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}" alt="{$illustration.legend|base64_decode|trim}" title="{$illustration.legend|base64_decode|trim}" style="width:200px"/>

				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-legend">{$illustration.legend|base64_decode|trim}</h3>
					<h3 class="ari-illustration-rights">©{$illustration.rights|base64_decode|trim}</h3>

				</div>
			</div>
		{/if}
		{/foreach}
	{/if}
	</div>

	<div class="ari-content">
		<span class="ari-publishDate">{$news.publicationDateFormatted.displayFormat}</span>
		<h2 class="ari-title">{$content.title}</h2>
		<div class="ari-text">
			{$content.description}
			{$content.text}
		</div>
	</div>	

</div>

