{if $video.siteId == 5 || $video.siteId == 4}
{assign var=urlDl value="http://www.afprelaxnews.com/"}
{/if}
{if $video.siteId == 6}
{assign var=urlDl value="http://www.relaxfil.com/"}
{/if}
{if $video.formats == ''}
<div class="ari-illustrations">
		<div class="ari-illustration video" style="text-align:center">
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
			</div>
		</div>
	</div>
{else}
{assign var=obj value=$video.getCurrentObj}
{assign var=obj value=$obj.object}
{*if $medias|@count gt 0*}
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="middle" align="center" width="310">
{foreach from=$video.relateds item=related}
			<img src="{$related.object->getPhotoUrlByFormat('w300')}" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
			<h4>{$related.relation.title}</h4>
			<h5>©{$related.object->credits}</h5>
			<br/>
			<p>{$related.object->specialUses}</p>
{/foreach}
			</td>
			<td valign="top">
				<div style="padding:5px;">
					<table width="100%" border="0"  cellspacing="0" cellpadding="0" >
						<tr>
							<td align="center" valign="middle">&nbsp;</td>
							<td align="center" valign="middle"><b>{'_MEDIA_EXTENSION'|constant}</b></td>
							<td align="center" valign="middle"><b>{'_MEDIA_WIDTH'|constant}</b></td>
							<td align="center" valign="middle"><b>{'_MEDIA_HEIGHT'|constant}</b></td>
							<td align="center" valign="middle"><b>{'_MEDIA_FORMAT'|constant}</b></td>
							<td align="center" valign="middle"><b>{'_MEDIA_SIZE'|constant}</b></td>
						</tr>
{assign var=formats value=$obj->getFormatAfpVideo()}
{foreach from=$formats item=extension}
{assign var=formatsvideo value=$obj->getAfpVideoByFormat($extension)}
{foreach from=$formatsvideo item=resolution}
{math assign="poids" equation='x/(y*y)' x=$resolution.size y=1024}
						<tr>
							<td align="center" valign="middle" style="height:25px;">
								<a href="{$urlDl}download_video.php?file={$resolution.url|urlencode}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/disk_blue.png" title="{'_DOWNLOAD_PICTURES'|constant}" /></a>
							</td>
							<td align="center" valign="middle" align="center">
								{$extension}
							</td>
							<td align="center" valign="middle" align="center">
								{$resolution.width}
							</td>
							<td align="center" valign="middle" align="center">
								{$resolution.height}
							</td>
							<td align="center" valign="middle" align="center">
{math assign="format" equation='round(x/y)' x=$resolution.width y=$resolution.height}
{if $format==1}
									4/3
{/if}
{if $format==2}
									16/9
{/if}
							</td>
							<td align="center" valign="middle" align="center">
								{$poids|round} Mo
							</td>
						</tr>
{/foreach}
{/foreach}
					</table>
				</div>
			</td>
		</tr>
	</table>
{*/if*}
{/if}
