{include file="demo/pages/channel.tpl" channel=$bizobject ochannel=$obizobject}
<script type="text/javascript">
window.onload = function()
{ldelim}
    portal = new ZoneSet('{$bizobject.className}', {$bizobject.id}, {ldelim}autoUpdate:true{rdelim});
    portal.update();
{rdelim}
</script>
