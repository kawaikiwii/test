{context values="article=`$bizobject.id`"}
{load class="channel" where="id=`$bizobject.channelId`"}
{loop class="chapter" of="article" where="rank=1"}
    {include file="demo/pages/chapter.tpl" article=$bizobject}
{/loop}
{/context}