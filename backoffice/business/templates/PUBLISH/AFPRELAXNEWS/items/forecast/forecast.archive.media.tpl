
<form name="downloadZip" method="POST" action="{$config.wcm.webSite.url}zipfile.php" target="_blank">
	<table border="1" width="100%" cellspacing="0" cellpadding="10">
		{if $forecast.properties.illustration|@count gt 0}
			{foreach from=$forecast.properties.illustration item=illustration}
				<tr>
					<td valign="top" align="center" width="200">
						<img src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}"  alt="{$illustration.legend|base64_decode|trim}" title="{$illustration.legend|base64_decode|trim}"/><h4>{$illustration.legend|base64_decode|trim}</h4>
        				<br>
						©{$illustration.rights|base64_decode|trim} | {$illustration.legend|base64_decode|trim}
					</td>
				</tr>
			{/foreach}
		{/if}
	</table>
</form>
