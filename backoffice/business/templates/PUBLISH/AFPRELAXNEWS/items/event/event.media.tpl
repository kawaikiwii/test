{if $event.siteId == 5 || $news.siteId == 4}
{assign var=urlDl value="http://www.afprelaxnews.com/"}
{/if}
{if $event.siteId == 6}
{assign var=urlDl value="http://www.relaxfil.com/"}
{/if}
<form id="downloadZip" name="downloadZip" method="POST" action="{$urlDl}zipfile.php" target="_blank">
{if $medias|@count gt 0}	
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
		<tr>
			<td valign="middle" align="center" width="310">
				<img src="{$related.object->getPhotoUrlByFormat('w300')}" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
				<h4>{$related.relation.title}</h4>
				<h5>©{$related.object->credits}</h5><br/>
				<h5>{'_MEDIA_SPECIAL_USES'|constant}</h5>
				<p>{$related.object->specialUses}</p>
			</td>
			<td valign="top">
{assign var=formats value=$related.object->getFormats()}
			<div style="padding:5px;">
				<table width="100%" border="0"  cellspacing="0" cellpadding="0" >
					<tr>
						<td width="25" align="center" valign="middle">&nbsp;</td>
						<td width="60" align="center" valign="middle">&nbsp;</td>
						<td align="center" valign="middle"><b>{'_MEDIA_HEIGHT'|constant}</b></td>
						<td align="center" valign="middle"><b>{'_MEDIA_WIDTH'|constant}</b></td>
						<td align="center" valign="middle"><b>{'_MEDIA_SIZE'|constant}</b></td>
					</tr>
					<tr>
						<td colspan="5"><h4 style="color:64247C;padding:5px 1px 3px 1px;">{'_MEDIA_ORIGINAL'|constant}</h4></td>
					</tr>
					<tr>
						<td align="center" valign="middle">
							<input type="checkbox" name="pics[]" value="{$formats.original.filename}" id="pic_{$related.relation.destinationId}.{$formats.original.format}"/>
						</td>
						<td align="center" valign="middle">
							<a href="{$formats.original.fileurl}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/view.png" title="{'_VIEW_PICTURES'|constant}" /></a>
							<a href="{$urlDl}download.php?id={$related.relation.destinationId}&format={$formats.original.format}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/disk_blue.png" title="{'_DOWNLOAD_PICTURES'|constant}" /></a>
						</td>
						<td align="center" valign="middle">{$formats.original.height}</td>
						<td align="center" valign="middle">{$formats.original.width}</td>
						<td align="center" valign="middle">{$formats.original.weight}</td>
					</tr>
					<tr>
						<td colspan="5"><h4 style="color:64247C;padding:5px 1px 3px 1px;">{'_MEDIA_FIXED_HEIGHT'|constant}</h4></td>
					</tr>
{foreach from=$formats.height item=height}
					<tr>
						<td align="center" valign="middle">
							<input type="checkbox" name="pics[]" value="{$height.filename}" id="pic_{$related.relation.destinationId}.{$height.format}"/>
						</td>
						<td align="center" valign="middle">
							<a href="{$height.fileurl}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/view.png" title="{'_VIEW_PICTURES'|constant}" /></a>
							<a href="{$urlDl}download.php?id={$related.relation.destinationId}&format={$height.format}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/disk_blue.png" title="{'_DOWNLOAD_PICTURES'|constant}" /></a>
						</td>
						<td align="center" valign="middle">{$height.height}</td>
						<td align="center" valign="middle">{$height.width}</td>
						<td align="center" valign="middle">{$height.weight}</td>
					</tr>
{/foreach}
					<tr>
						<td colspan="5"><h4 style="color:64247C;padding:5px 1px 3px 1px;">{'_MEDIA_FIXED_WIDTH'|constant}</h4></td>
					</tr>
{foreach from=$formats.width item=width}
					<tr>
						<td align="center" valign="middle">
							<input type="checkbox" name="pics[]" value="{$width.filename}" id="pic_{$related.relation.destinationId}.{$width.format}"/>
						</td>
						<td align="center" valign="middle">
							<a href="{$width.fileurl}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/view.png" title="{'_VIEW_PICTURES'|constant}" /></a>
							<a href="{$urlDl}download.php?id={$related.relation.destinationId}&format={$height.format}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/disk_blue.png" title="{'_DOWNLOAD_PICTURES'|constant}" /></a>
						</td>
						<td align="center" valign="middle">{$width.height}</td>
						<td align="center" valign="middle">{$width.width}</td>
						<td align="center" valign="middle">{$width.weight}</td>
					</tr>
{/foreach}
					<tr>
						<td colspan="5"><h4 style="color:64247C;padding:5px 1px 3px 1px;">{'_MEDIA_SQUARE'|constant}</h4></td>
					</tr>
{foreach from=$formats.square item=square}
					<tr>
						<td align="center" valign="middle">
							<input type="checkbox" name="pics[]" value="{$square.filename}" id="pic_{$related.relation.destinationId}.{$square.format}"/>
						</td>
						<td align="center" valign="middle">
							<a href="{$square.fileurl}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/view.png" title="{'_VIEW_PICTURES'|constant}" /></a>
							<a href="{$urlDl}download.php?id={$related.relation.destinationId}&format={$square.format}" target="_blank"><img src="{$config.wcm.webSite.urlRepository}images/default/16x16/disk_blue.png" title="{'_DOWNLOAD_PICTURES'|constant}" /></a>
						</td>
						<td align="center" valign="middle">{$square.height}</td>
						<td align="center" valign="middle">{$square.width}</td>
						<td align="center" valign="middle">{$square.weight}</td>
					</tr>
{/foreach}
				</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr style="border-color:#64247C"/></td>
		</tr>
{/if}
{/foreach}
	</table>
{/if}
</form>
