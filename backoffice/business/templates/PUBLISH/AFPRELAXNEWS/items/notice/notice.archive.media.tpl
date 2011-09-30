<form name="downloadZip" method="POST" action="{$config.wcm.webSite.url}zipfile.php" target="_blank">
<table border="1" width="100%" cellspacing="0" cellpadding="10">
        {if $notice.properties.illustration|@count gt 0}
        {foreach from=$notice.properties.illustration item=illustration}
        <tr>
            <td valign="top" align="center" width="200">
                <img src="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}" alt="{$illustration.legend|base64_decode|trim}" title="{$illustration.legend|base64_decode|trim}"/><h4>{$illustration.legend|base64_decode|trim}</h4>
                <br>
                ©{$illustration.rights|base64_decode|trim} | {$illustration.legend|base64_decode|trim}
            </td>
            <td valign="top">
                <table width="400">
                    <tr>
                        <td>
                            <a href="{$config.wcm.webSite.url}download.php?dir={$config.wcm.webSite.repository}illustration/photo/archives/&pic={$illustration.quicklook}" target="_blank"><img src="/inc/images/disk_blue.gif" title="Download" /></a>
                        </td>
                        <td>
                            <a href="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$illustration.quicklook}" target="_blank"><img src="/inc/images/view.gif" title="View" /></a>
                        </td>
                        <td>
                            <input type="checkbox" name="pics[]" value="{$config.wcm.webSite.repository}illustration/photo/archives/{$illustration.quicklook}" id="pic_{$illustration.quicklook}" />
                            <label for="pic_{$illustration.quicklook}">
                                <a href="#downloadZipFile" style="color:blue; text-decoration:underline;">&nbsp;&nbsp;Add</a>
                            </label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        {/foreach}
        {/if}
    </table>
</form>
	