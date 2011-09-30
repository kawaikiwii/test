
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
<div id="preview-{$className}_{$event.id}" class="ari-preview ari-{$className}{if $medias|@count gt 0} ari-illustrated{/if}">
	<div class="ari-details">
		<span class="ari-pilar ari-{$event.mainChannelCss}">{$event.categorization.parentChannel_title}</span>
		<span class="ari-separator">|</span>
		<span class="ari-channel">{$event.categorization.mainChannel_title}</span>
	</div>
	<div class="ari-illustrations">
{if $medias|@count gt 0}
{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
{assign var=widthAndHeight value=$related.object->getWidthAndHeight('w400')}
					<div class="ari-illustration photo">
						<div class="ari-illustration-item"> 
							<img src="{$related.object->getPhotoUrlByFormat('w400')}" alt="{$related.object->title|htmlspecialchars}" title="{$related.object->title|htmlspecialchars}"/>
						</div>
						<div class="ari-illustration-detail">
							<h3 class="ari-illustration-legend">{$related.relation.title}</h3>
{if $related.relation.media_description}
							<h3 class="ari-illustration-legend" style="font-size:9px;">{$related.relation.media_description}</h3>
{/if}
							<h3 class="ari-illustration-rights">©{$related.object->credits}</h3>
{if $related.object->specialUses}
								<table cellpadding=0 cellspacing=0 style="padding:7px; text-align:left;">
									<tr><td style="padding-top:7px;"><b>{'_MEDIA_SPECIAL_USES'|constant}</b></td></tr>
									<tr><td>{$related.object->specialUses}</td></tr>
								</table>
{/if}
						</div>
					</div>
{/if}
{/foreach}
{else}
{assign var="cheminDeFer" value="ok"}
			<div class="ari-illustration photo">
				<div class="ari-illustration-item"> 
					<div style="width:220px; height:220px;">&nbsp;</div>
				</div>
				<div class="ari-illustration-detail">
					<h3 class="ari-illustration-legend">&nbsp;</h3>
				</div>
			</div>
{/if}
	</div>
{assign var=lieuTitle value=""}
{assign var=lieuAddress_1 value=""}
{assign var=lieuAddress_1 value=""}
{assign var=lieuZipcode value=""}
{assign var=lieuCity value=""}
{assign var=lieuCountry value=""}
{assign var=lieuPhone value=""}
{assign var=lieuEmail value=""}
{assign var=lieuWebsite value=""}
{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'location'}
{assign var=lieuTitle value=$related.object->title}
{assign var=lieuAddress_1 value=$related.object->address_1}
{assign var=lieuAddress_2 value=$related.object->address_2}
{assign var=lieuZipcode value=$related.object->zipcode}
{assign var=lieuCity value=$related.object->city}
{assign var=lieuCountry value=$related.object->country}
{assign var=lieuPhone value=$related.object->phone}
{assign var=lieuEmail value=$related.object->email}
{assign var=lieuWebsite value=$related.object->website}
{assign var=siteId value=$event.siteId}
{assign var=language value=""}
{php}
$site = new site();
$site->refresh($this->get_template_vars('siteId'));
$this->assign('language', $site->language);
$lieuCity = $this->get_template_vars('lieuCity');
if($lieuCity != "" && $site->language == "fr") {
$url2 = "http://api.geonames.org/search?q=".rawurlencode(utf8_encode($lieuCity)) ."&lang=fr&username=spajot";
$page2 = file_get_contents($url2);
$xml2 = new SimpleXMLElement($page2);
$cityName = $xml2->geoname->name;
$this->assign('lieuCity', $cityName);
}
$lieuCountry = $this->get_template_vars('lieuCountry');
if($lieuCountry != "" && $site->language == "fr") {
$url = "http://api.geonames.org/search?q=".rawurlencode(utf8_encode($lieuCountry)) ."&lang=fr&username=spajot";
$page = file_get_contents($url);
$xml = new SimpleXMLElement($page);
$countryName = $xml->geoname->name;
$this->assign('lieuCountry', $countryName);
}
{/php}
{/if}
{/foreach}
	<div class="ari-content">
			<h2 class="ari-title">{$content.title} {if $lieuCity != ""}({$lieuCity}){/if}</h2>
			<h4 class="ari-slugline">{foreach from=$event.slugLine item=slug name=foo}{if $slug.type == 'channel'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}channelIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'thema'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'target'}
			<a href="#" onclick="ARe.search.f('{$slug.title}', {literal}{{/literal}listIds:{$slug.id}{literal}}{/literal})">{$slug.title}</a>
{/if}{if $slug.type == 'folder'}
			<a href="#" onclick="ARe.folder.open(this, {$slug.id})">{$slug.title}</a>
{/if}{if !$smarty.foreach.foo.last} - {/if}{/foreach}</h4>
			<div class="ari-text"{if isset($cheminDeFer)} style="margin-left:300px;"{/if}>
{assign var=source value="("}
{if $event.sourceLocation != ""}{assign var=source value="`$source`<span class='ari-source ari-source-location''>`$event.sourceLocation`</span><span class='ari-source ari-separator''>-</span>"}{/if}
{assign var=source value="`$source`<span class='ari-source ari-source-name'>`$event.sourceLabel`</span>)"}	
{assign var=description value=$content.description}{php}
$source = ($this->get_template_vars('source'));
$description = ($this->get_template_vars('description'));
$description = preg_replace("/\<p\>/i", "<p>".$source." - ",$description, 1);
$this->assign('description', $description);
{/php}
			{$description}
				{$content.text}
			</div>
		</div>	
	<div class="ari-informations">
		<div class="ari-date ari-section">
			<h2 class="ari-title">{'_EVENT_DATES'|constant}</h2>
			<div class="ari-start">
				<span>{'_EVENT_START'|constant} : </span>
				<span>{$event.startDate|date_format:"%d-%m-%Y"}</span>
			</div>
{if $event.startDate != $event.endDate}
			<div class="ari-end">
				<span>{'_EVENT_END'|constant} : </span>
				<span>{$event.endDate|date_format:"%d-%m-%Y"}</span>
			</div>
{/if}
			<div class="ari-comment">{$event.commentDate}</div>
		</div>
{if $lieuTitle != ""}
		<div class="ari-price ari-section">
			<h2 class="ari-title">{'_BIZ_LOCATION'|constant}</h2>
			{if $lieuTitle != ""}<span>{$lieuTitle}</span><br/>{/if}
			{if $lieuAddress_1 != ""}<span>{$lieuAddress_1}</span><br/>{/if}
			{if $lieuAddress_2 != ""}<span>{$lieuAddress_2}</span><br/>{/if}
			{if $lieuZipcode != ""}<span>{$lieuZipcode}</span><br/>{/if}
			{if $lieuCity != ""}<span>{$lieuCity}</span><br/>{/if}
			{if $lieuCountry != ""}<span>{$lieuCountry}</span><br/>{/if}
		</div>
{/if}
{if $event.price != ""}
		<div class="ari-price ari-section">
			<h2 class="ari-title">{'_EVENT_PRICE'|constant}</h2>
			<span>{$event.price}</span>	
		</div>
{/if}
{if $event.phone != "" || $event.email != "" || $event.website != "" || $lieuPhone != "" || $lieuEmail != "" || $lieuWebsite != ""}
		<div class="ari-contact ari-section">
			<h2 class="ari-title">{'_EVENT_CONTACT'|constant}</h2>
{if $event.phone != "" || $lieuPhone != ""}
			<div class="ari-phone">
				<span>{'_EVENT_CONTACT_PHONE'|constant} : </span>
				<span>{if $event.phone != ""}{$event.phone}{else}{$lieuPhone}{/if}</span>
			</div>
{/if}
{if $event.email != "" || $lieuEmail != ""}
			<div class="ari-email">
				<span>{'_EVENT_CONTACT_MAIL'|constant} : </span>
				<span><a href="mailto:{if $event.email != ''}{$event.email}{else}{$lieuEmail}{/if}" target="_blank">{if $event.email != ""}{$event.email}{else}{$lieuEmail}{/if}</a></span>
			</div>
{/if}
{if $event.website != "" || $lieuWebsite != ""}
			<div class="ari-website">
				<span>{'_EVENT_CONTACT_WEBSITE'|constant} : </span>
				<span><a href="{if $event.website != ''}{$event.website}{else}{$lieuWebsite}{/if}" target="_blank">{if $event.website != ""}{$event.website}{else}{$lieuWebsite}{/if}</a></span>
			</div>
{/if}
		</div>
{/if}
	</div>
</div>