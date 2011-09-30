{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'location'}
{assign var="locationId" value=$related.relation.destinationId}
{php}
$locationId = $this->get_template_vars('locationId');
$oLocation = new location(null, $locationId);
$locationTitle = $oLocation->title;
$this->assign('locationTitle', $locationTitle);
{/php}
{/if}
{/foreach}
{assign var="channelIds" value=$event.channelIds|unserialize}
{assign var="channelIds" value="-"|implode:$channelIds}
{assign var="channelId" value=$event.channelId}
<div cid="{$event.id}" classname="{$className}" channelId="{$channelId}" channelIds="-{$channelIds}-" id="list-{$className}_{$event.id}" class="ari-item ari-not-allowed" onclick="ARe.pview('{$className}', {$event.id});" ondblclick="ARe.popview('{$className}', {$event.id});">
	<span class="ari-category">[{$event.categorization.mainChannel_title}]</span>
	<h2 class="ari-title">{$content.title} {if isset($locationTitle)}({$locationTitle}){/if}</h2>
	<h3 class="ari-schedule"><span class="">From {$event.startDate|date_format:"%d-%m-%Y"} to {$event.endDate|date_format:"%d-%m-%Y"}</span></h3>
{if $medias|@count gt 0}
		<div class="ari-illustrations">
{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
					<div class="ari-illustration photo">
						<div class="ari-illustration-item"> 
							<img src="{$related.object->getPhotoUrlByFormat('w50')}" width="50" alt="{$related.relation.title|htmlspecialchars} - ©{$related.object->credits|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars} - ©{$related.object->credits|htmlspecialchars}"/>
						</div>
					</div>
{/if}
{/foreach}
		</div>
{/if}
	<div class="ari-content">
{$content.description}
	</div>
</div>
