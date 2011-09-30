<li class="{$bo->getClass()}">
    <a href="?_wcmAction=business/{$bo->getClass()}&id={$bo->id}">
        {if $bo->firstname}{$bo->firstname}{/if}
        {if $bo->lastname} {$bo->lastname}{/if}
        {if $bo->username} ({$bo->username}){/if}
    </a>
</li>