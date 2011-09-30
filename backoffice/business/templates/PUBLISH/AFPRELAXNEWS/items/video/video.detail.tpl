<div id="preview-{$className}_{$video.id}" class="ari-preview ari-{$className}">
	<div class="ari-details">
		<span class="ari-publishDate">{$video.publicationDateFormatted.displayFormat}</span>
		<span class="ari-pilar ari-{$video.mainChannelCss}">{$video.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$video.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
		<div class="ari-illustration video">
			<div class="ari-illustration-item"> 
{assign var="embedOriginal" value=$video.embed}
{php}
$embedOriginal = $this->get_template_vars('embedOriginal');
$embedSkinned = str_replace('.flv', '.flv&backcolor=64247C&frontcolor=FFFFFF&lightcolor=64247C&screencolor=64247C', $embedOriginal);
$this->assign('embedSkinned', $embedSkinned);

$part1 = substr($embedOriginal, strpos($embedOriginal, 'file=')+5);
$flvFile = substr($part1, 0, strpos($part1, '.flv')+4);
$this->assign('flvFile', $flvFile);
{/php}
				{$embedSkinned}
			</div>
			<div class="ari-illustration-detail">
				<h3 class="ari-illustration-legend">{$video.title}</h3>
				<h3 class="ari-illustration-rights">©{$video.credits}</h3>
{if $video.formats != ''}
				<h3><a href="#" onclick="ARe.mediaview('video',{$video.id})">Download video</a></h3>
{/if}
			</div>
		</div>
{if $video.formats == ''}
		<textarea cols="60" rows="5">{$video.embed}</textarea>
{/if}
	</div>
	<div class="ari-content">
		<h2 class="ari-title">{$content.title}</h2>
		<h4 class="ari-slugline">{foreach from=$video.slugLine item=slug name=foo}{if $slug.type == 'channel'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}channelIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'thema'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'target'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'folder'}
			<a href="#" onclick="ARe.folder.open(this, {$slug.id})">{$slug.title}</a>
{/if}{if !$smarty.foreach.foo.last} - {/if}{/foreach}</h4>
		<div class="ari-description">
{assign var=source value=""}
{if $video.sourceLocation != ""}
{assign var=source value="`$source`("}
{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$video.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$video.sourceLabel`</span>)"}
{/if}
{assign var=description value=$content.description}{php}
$source = ($this->get_template_vars('source'));
if(!empty($source))
$source .= " - ";
$description = ($this->get_template_vars('description'));
$description = preg_replace("/\<p\>/i", "<p>".$source,$description, 1);
$this->assign('description', $description);
{/php}
			{$description}
			{$content.text}
		</div>
	</div>	
</div>
