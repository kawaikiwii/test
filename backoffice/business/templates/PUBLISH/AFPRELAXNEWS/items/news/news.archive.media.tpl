<form name="downloadZip" method="POST" action="{$config.wcm.webSite.url}zipfile.php" target="_blank">
    <table border="1" width="100%" cellspacing="0" cellpadding="10">
        {if $news.properties.illustration|@count gt 0}
        {foreach from=$news.properties.illustration item=illustration}
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
                            <ul>
                                {foreach from=$illustration key=k item=v}
                                {if $k != "legend" && $k != "rights"}
                                <li>
                                    <a href="{$config.wcm.webSite.urlRepository}illustration/photo/archives/{$v}" target="_blank">{$k}</a>
                                </li>
                                {/if}
                                {/foreach}
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {/foreach}
        {/if}
    </table>
</form>
