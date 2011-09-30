{if $object->publicationId && $object->issueId}
	{assign var=publication value=$object->getAssoc_publication(false)}
	{assign var=issue value=$object->getAssoc_issue(false)}
	<li class="detail">
		<span class="label">{'_BIZ_PUBLISHED'|constant} {'_WEB_IN'|constant}</span>
			<em>{$publication.title}</em>, {'_BIZ_ISSUENUMBER'|constant|lower} <em>{$issue.issueNumber}</em>
		</ul>
	</li>
{/if}
