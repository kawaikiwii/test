

<div id="preview-{$className}_{$forecast.id}" class="ari-preview ari-{$className}{if $forecast.properties.illustration|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$forecast.mainChannelCss}">{$forecast.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$forecast.categorization.mainChannel_title}</span>
	</div>

	<div class="ari-illustrations">
	{if $forecast.properties.illustration|@count gt 0}
		
		{foreach from=$forecast.properties.illustration item=illustration}
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
		<span class="ari-publishDate">{$forecast.startDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>
		{if $forecast.startDate != $forecast.endDate}
			- <span class="ari-publishDate">{$forecast.endDate|date_format:"%d-%m-%Y %H:%M:%S"}</span>			
		{/if}
		<h2 class="ari-title">{$content.title}</h2>
		<div class="ari-text">
			{$content.description}
			{$content.text}
		</div>
		
	</div>	

</div>

