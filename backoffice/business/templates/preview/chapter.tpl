{context values="article=`$bizobject.articleId`"}
{load class="channel" where="id=`$bizobject.article.channelId`"}
{include file="demo/pages/chapter.tpl" chapter=$bizobject article=$bizobject.article}
{/context}