{assign var="objectClass" value=$object->getClass()}
{assign var="itemSelector" value="`$objectClass`_`$object->id`"}


    <div class="preview bundle">

        <h1 class="{$objectClass}">{$object->lastname}, {$object->firstname}</h1>
        
        <ul>
            <li><strong>{"_BIZ_USERNAME"|constant}:</strong> {$object->username}</li>
            <li><strong>{"_BIZ_CONTRIBUTIONS"|constant}:</strong> {$object->getContributionCount()}</li>
            <li>{if $object->email}{$object->email}{else}<span class="empty">[{"_BIZ_EMAIL"|constant}]</span>{/if}</li>
            <li>{if $object->address}{$object->address}{else}<span class="empty">[{"_BIZ_ADDRESS"|constant}]</span>{/if}</li>
            <li>{if $object->city}{$object->city}{else}<span class="empty">[{"_BIZ_CITY"|constant}]</span>{/if}, {if $object->state}{$object->state}{else}<span class="empty">[{"_BIZ_STATE_PROVINCE"|constant}]</span>{/if}</li>
            <li>{if $object->country}{$object->country}{else}<span class="empty">[{"_BIZ_COUNTRY"|constant}]</span>{/if}, {if $object->postalCode}{$object->postalCode}{else}<span class="empty">[{"_BIZ_POSTALCODE"|constant}]</span>{/if}</li>
            <li>{if $object->phone}{$object->phone}{else}<span class="empty">[{"_BIZ_PHONE_NUMBER"|constant}]</span>{/if}</li>
        </ul>
        

    </div>
    
