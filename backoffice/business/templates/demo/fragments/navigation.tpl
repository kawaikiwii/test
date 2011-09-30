{loop name="navitem" class="channel" of="site" order="rank"}
    <li>
        <a href="{$navitem|@wcm:url}"<?php if ($channelId=={$navitem.id}) echo ' class="active"';?>>{$navitem.title}</a>
    </li>
{/loop}