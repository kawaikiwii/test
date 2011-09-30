{assign var=semanticData value=$object->semanticData}
{if $semanticData}
	<li class="detail">
		<span class="label">{'_BIZ_TME'|constant}</span>
		<ul>
			<li><span class="label">{'_BIZ_LANGUAGE'|constant}</span>
				{$semanticData->language|lower|capitalize}
			</li>
			<li><span class="label">{'_BIZ_TME_SENTIMENT_TONE'|constant}</span>
				{* @todo JFG Use threshold from configuration. Where is that configuration? *}
				{if $semanticData->tone > 0}
				    {'_BIZ_TME_SENTIMENT_TONE_POSITIVE'|constant}
				{elseif $semanticData->tone < 0}
                    {'_BIZ_TME_SENTIMENT_TONE_NEGATIVE'|constant}
				{else}
                    {'_BIZ_TME_SENTIMENT_TONE_NEUTRAL'|constant}
				{/if}
			</li>
			<li><span class="label">{'_BIZ_TME_SENTIMENT_SUBJECTIVITY'|constant}</span>
                {* @todo JFG Use threshold from configuration. Where is that configuration? *}
                {if $semanticData->subjectivity > 0}
                    {'_BIZ_TME_SENTIMENT_SUBJECTIVITY_OPINION'|constant}
                {else}
                    {'_BIZ_TME_SENTIMENT_SUBJECTIVITY_FACT'|constant}
                {/if}
			</li>
			<li><span class="label">{'_BIZ_TME_ENTITITES_ON'|constant}</span>
				<ul>
                {if $semanticData->ON|@count > 0}
					{foreach from=$semanticData->ON item=organisation key=on_value}
						<li>{$on_value}</li>
					{/foreach}                            
                {else}
                    {'_BIZ_NO_DETAIL'|constant}
                {/if}
				</ul>
			</li>
			<li><span class="label">{'_BIZ_TME_ENTITITES_PN'|constant}</span>
				<ul>
                {if $semanticData->PN|@count > 0}
					{foreach from=$semanticData->PN item=person key=pn_value}
						<li>{$pn_value}</li>
					{/foreach}                            
                {else}
                    {'_BIZ_NO_DETAIL'|constant}
                {/if}
				</ul>
			</li>
			<li><span class="label">{'_BIZ_TME_ENTITITES_GL'|constant}</span>
				<ul>
                {if $semanticData->GL|@count > 0}
					{foreach from=$semanticData->GL item=place key=gl_value}
						<li>{$gl_value}</li>
					{/foreach}                            
                {else}
                    {'_BIZ_NO_DETAIL'|constant}
                {/if}
				</ul>
			</li>
			<li><span class="label">{'_BIZ_TME_CONCEPTS'|constant}</span>
				<ul>
                {if $semanticData->concepts|@count > 0}
					{foreach from=$semanticData->concepts item=concepts key=c_value}
						<li>{$c_value}</li>
					{/foreach}                            
                {else}
                    {'_BIZ_NO_DETAIL'|constant}
                {/if}
				</ul>
			</li>
		</ul>
	</li>
{/if}
