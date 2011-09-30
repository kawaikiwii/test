
<?xml version="1.0"?>
<diaporama>
	<parameters>
		<document margin="10" padding="10" backgroundColor="0x000000" resizeImage="false"/>
		<shadow enabled="true" size="10" opacity="50" color="0x000000"/>
		<elements opacity="50" backgroundColor="0x000000"/>
		<buttons enabled="true" position="top"/>
		<playback enabled="true" timing="{"2"|mt_rand:5}" transition="{"1"|mt_rand:4}"/>
		<caption enabled="true" color="0xffffff" fontFamily="Verdana" fontSize="9" position="bottom" />
		<description color="0xffffff" fontFamily="Verdana" fontSize="7" />
	</parameters>
	<items>
		{foreach from=$slideshow.relateds item=related}
		<item src="{$related.object->getPhotoUrlByFormat('w250')}">
			<caption>{if $related.relation.title eq ''} {$content.title} {else} {$related.relation.title}  {/if}</caption>
			<description>©{$related.object->credits}</description>
		</item>
		{/foreach}
	</items>
</diaporama>