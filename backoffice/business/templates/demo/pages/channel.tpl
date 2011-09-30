{load class="site" where="id=`$channel.siteId`"}
{include file="demo/blocks/header.tpl" bizobject=$channel}
<div id="body" class="homepage">
    {include file="`$channel.templateId`" obizobject="$ochannel"}
</div>
{include file="demo/blocks/footer.tpl"}
