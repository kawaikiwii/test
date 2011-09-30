    <h4>{'_BIZ_TME'|constant}</h4>
    <dl>
        <dt>{'_BIZ_TME_ENTITITES_PN'|constant}</dt>
        <dd>
            {if $bizobject.semanticData.PN|@count > 0}
	            {foreach from=$bizobject.semanticData.PN key=data item=item name=pn}
                    {if $data != ''}
    	                {$data}{if !$smarty.foreach.pn.last},{/if}
		            {else}
		                {'_BIZ_NO_DETAIL'|constant}
		            {/if}
	            {/foreach}
            {else}
                {'_BIZ_NO_DETAIL'|constant}
            {/if}
            <br/>
            <br/>
        </dd>
        <dt>{'_BIZ_TME_ENTITITES_GL'|constant}</dt>
        <dd>
            {if $bizobject.semanticData.GL|@count > 0}
	            {foreach from=$bizobject.semanticData.GL key=data item=item name=gl}
                    {if $data != ''}
	                   {$data}{if !$smarty.foreach.gl.last},{/if}
                    {else}
                        {'_BIZ_NO_DETAIL'|constant}
                    {/if}
	            {/foreach}
            {else}
                {'_BIZ_NO_DETAIL'|constant}
            {/if}
            <br/>
            <br/>
        </dd>
        <dt>{'_BIZ_TME_ENTITITES_ON'|constant}</dt>
        <dd>
            {if $bizobject.semanticData.ON|@count > 0}
	            {foreach from=$bizobject.semanticData.ON key=data item=item name=org} {* Note 'on' as a loop name is invalid! *}
                    {if $data != ''}
    	                {$data}{if !$smarty.foreach.org.last},{/if}
                    {else}
                        {'_BIZ_NO_DETAIL'|constant}
                    {/if}
            {/foreach}
            {else}
                {'_BIZ_NO_DETAIL'|constant}
            {/if}
        </dd>
    </dl>
