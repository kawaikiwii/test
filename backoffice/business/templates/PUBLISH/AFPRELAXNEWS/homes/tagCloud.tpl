{capture name="TAGCLOUD"}
<tags>
{foreach from=$CHANNELS item=chan}
{if $chan->css == "wellbeing"}{assign var=color value="0x43D743"}{/if}
{if $chan->css == "househome"}{assign var=color value="0xF96913"}{/if}
{if $chan->css == "entertainment"}{assign var=color value="0xFFB7DB"}{/if}
{if $chan->css == "tourism"}{assign var=color value="0x6699FF"}{/if}
	<a href="javascript:ARe.search.rubric('{$chan->title}', 'news,slideshow,event,video', {$chan->id})" style="font-size: 15pt;" color="{$color}" hicolor="0xff0000">{$chan->title}</a>
{/foreach}
{foreach from=$FOLDERS item=fold}
	<a href="javascript:ARe.folder.open('{$fold->title}', {$fold->id})" style="font-size: 15pt;" color="0x64247C" hicolor="0xff0000">{$fold->title}</a>	
{/foreach}
</tags>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/tagCloud.xml"}
{dump file=$filename content=$smarty.capture.TAGCLOUD utf8=true}