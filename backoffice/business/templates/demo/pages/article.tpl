{load class="site" where="id=`$article.siteId`"}
{load class="channel" where="id=`$article.channelId`"}
{loop class="chapter" of="article" orderby="rank"}
{if $loop.chapter.iteration == 1}
    {include file="demo/pages/chapter.tpl"}
{else}
  {assign var="filename" value="`$chapter.rank`.php"}
  {capture name="chapterPage"}
      {include file="demo/pages/chapter.tpl" channel=$channel site=$site}
  {/capture}
  {dump file="`$this->outputFolder`$filename" content=$smarty.capture.chapterPage utf8=true}
{/if}
{/loop}
