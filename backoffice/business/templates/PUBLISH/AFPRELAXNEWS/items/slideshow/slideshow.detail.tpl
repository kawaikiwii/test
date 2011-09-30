
<div id="preview-{$className}_{$slideshow.id}" class="ari-preview ari-{$className}">
	<div class="ari-details">
{assign var="slideshowHeight" value="350"}
{assign var="slideshowWidth" value="350"}			
		<span class="ari-publishDate">{$slideshow.publicationDateFormatted.displayFormat}</span>
		<span class="ari-pilar ari-{$slideshow.mainChannelCss}">{$slideshow.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$slideshow.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
		<div class="ari-illustration slideshow">
			<div class="ari-illustration-item">
				<object name="ars-{$className}_{$slideshow.id}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" id="ars-{$className}_{$slideshow.id}" height="{$slideshowHeight}" width="{$slideshowWidth}">
					<param name="movie" value="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$xmlSlideshowUrl}"><param name="quality" value="high"><param name="wmode" value="transparent">
					<embed src="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$xmlSlideshowUrl}" height="{$slideshowHeight}" width="{$slideshowWidth}" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="ars-{$className}_{$slideshow.id}" swliveconnect="true" wmode="transparent"></embed>
				</object>
			</div>
			<div class="ari-illustration-detail">
				<div class="ari-embed"><input type='text' onClick='this.select();' value='<object name="ars-{$className}_{$slideshow.id}" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" id="ars-{$className}_{$slideshow.id}" height="{$slideshowHeight}" width="{$slideshowWidth}"><param name="movie" value="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$xmlSlideshowUrl}"><param name="quality" value="high"><param name="wmode" value="transparent"><embed src="{$config.wcm.webSite.urlRepository}swf/diaporama.swf?datas={$xmlSlideshowUrl}" height="{$slideshowHeight}" width="{$slideshowWidth}" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" name="ars-{$className}_{$slideshow.id}" swliveconnect="true" wmode="transparent"></embed></object>'/></div>
			</div>
		</div>
	</div>
	<div class="ari-content">
		<h2 class="ari-title">{$content.title}</h2>
		<h4><span class="ari-category">{$medias|@count} photos</span></h4>
		<h4 class="ari-slugline">{foreach from=$slideshow.slugLine item=slug name=foo}{if $slug.type == 'channel'}
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
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$slideshow.sourceLabel`</span>)"}	
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
	<div class="ari-components">
{foreach from=$slideshow.relateds item=related}
{assign var=widthAndHeight value=$related.object->getWidthAndHeight('w250')}
		<div class="ari-component">
			<div class="ari-illustration photo">
				<div class="ari-illustration-item"> 
					<img src="{$related.object->getPhotoUrlByFormat('w250')}" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-rights">©{$related.object->credits}</h3>
					{if $related.object->specialUses}<h3 class="ari-illustration-rights">{'_MEDIA_SPECIAL_USES'|constant} : <i>{$related.object->specialUses}</i></h3>{/if}
				</div>
			</div>
			<div class="ari-content">
				<h2 class="ari-title">{$related.relation.title}</h2>
				{$related.relation.media_description}
				{$related.relation.media_text}
				<a href="{$related.relation.media_link}">{$related.relation.media_link}</a>
			</div>
		</div>
{/foreach}
		<div class="ari-component">
			<div class="ari-illustration photo">
			</div>
			<div class="ari-content">
			</div>
		</div>
	</div>
</div>
	