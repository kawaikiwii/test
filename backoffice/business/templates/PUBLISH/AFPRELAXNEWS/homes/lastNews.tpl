{capture name="LASTNEWS"}
{assign var="filename" value=$LASTNEWS->permalinks|replace:'%format%':'detail'}
{assign var="filename" value=`$config.wcm.webSite.repository``$filename`}
{if $filename|file_exists}
{assign var="filecontent" value=$filename|file_get_contents}
{$filecontent}
{/if}
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/lastNews.html"}
{dump file=$filename content=$smarty.capture.LASTNEWS utf8=true}