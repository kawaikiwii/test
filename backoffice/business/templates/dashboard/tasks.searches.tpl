<table cellspacing="0">
    {loop class="wcmSavedSearch" name="search" where="userId=`$session.userId` OR shared='1'" order="name"}
    {assign var=jsonShowUi value=$search.showui}
    {assign var=userId value=$session.userId}
    {php}
    $jsonShowUi = $this->get_template_vars('jsonShowUi');
    if (in_array($this->get_template_vars('userId'),($jsonShowUi == "") ? array() : json_decode($jsonShowUi)))
    {
    {/php}
    <tr{if $search.userId==$session.userId} class='alternate'{/if}>
        <td> <a class="search" href="{$config.wcm.backOffice.url}?_wcmAction=business/search&_wcmTodo=initSearch&search_query={$search.queryString}">{$search.name}</a> </td>
    </tr>
    {php}
    }
    {/php}
    {/loop}
</table>