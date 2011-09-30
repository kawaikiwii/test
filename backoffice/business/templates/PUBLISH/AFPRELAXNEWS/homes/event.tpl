
{capture name="OURSELECTION"}
{foreach from=$OURSEL item=event}
{assign var="filename" value=$event.permalinks|replace:'%format%':'detail'}
{assign var="filename" value=`$config.wcm.webSite.repository``$filename`}
{if $filename|file_exists}
{assign var="filecontent" value=$filename|file_get_contents}
{$filecontent}
{/if}
{/foreach}	
{/capture}
{assign var="ourSelectionFilename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/event.ourSelection.html"}
{dump file=$ourSelectionFilename content=$smarty.capture.OURSELECTION utf8=true}
{capture name="MUSTSEE"}
{foreach from=$MUSTSE item=event}
{assign var="filename" value=$event.permalinks|replace:'%format%':'list'}
{assign var="filename" value=`$config.wcm.webSite.repository``$filename`}
{if $filename|file_exists}
{assign var="filecontent" value=$filename|file_get_contents}
{$filecontent}
{/if}
{/foreach}	
{/capture}
{assign var="mustSeeFilename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/event.mustSee.html"}
{dump file=$mustSeeFilename content=$smarty.capture.MUSTSEE utf8=true}
