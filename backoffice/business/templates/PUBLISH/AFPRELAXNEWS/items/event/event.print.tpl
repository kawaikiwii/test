
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
<table>
<tr><td valign="top">
	<font size="2">{$event.sourceLabel} | {$event.publicationDateFormatted.displayFormat}</font>
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
	<h1 style="margin-top:0;">{$content.title} {if $lieuCity != ""}({$lieuCity}){/if}</h1>
	<table><tr>
{if $medias|@count gt 0}
			<td valign="top" width="300">
{foreach from=$event.relateds item=related}
{if $related.relation.destinationClass == 'photo'}
					<table border="1" width="100%"><tr><td valign="top">
						<img src="{$related.object->getPhotoUrlByFormat('w400')}" width="400" alt="{$related.relation.title|htmlspecialchars}" title="{$related.relation.title|htmlspecialchars}"/>
					</td></tr><tr><td valign="top">
						©{$related.object->credits}<br>
						<b>{$related.relation.title}</b>
						{if $related.relation.media_description}<br>{$related.relation.media_description}{/if}
					</td></tr></table>
{/if}
{/foreach}
			</td>
{/if}
		<td valign="top">
			{$content.description}
			{$content.text}
			<h3>{'_EVENT_DATES'|constant}</h3>
				<b>{'_EVENT_START'|constant} : </b><br>
				{$event.startDate|date_format:"%d-%m-%Y"}
{if $event.startDate != $event.endDate}
				<b>{'_EVENT_END'|constant} : </b><br>
				{$event.endDate|date_format:"%d-%m-%Y"}
{/if}
			<br>{$event.commentDate}
{if $lieuTitle != ""}
		<h3>{'_BIZ_LOCATION'|constant}</h3>
		{if $lieuTitle != ""}{$lieuTitle}<br/>{/if}
		{if $lieuAddress_1 != ""}{$lieuAddress_1}<br/>{/if}
		{if $lieuAddress_2 != ""}{$lieuAddress_2}<br/>{/if}
		{if $lieuZipcode != ""}{$lieuZipcode}<br/>{/if}
		{if $lieuCity != ""}{$lieuCity}<br/>{/if}
		{if $lieuCountry != ""}{$lieuCountry}<br/>{/if}
{/if}
{if $event.price != ""}
				<h3>{'_EVENT_PRICE'|constant}</h3>
				{$event.price}
{/if}
{if $event.phone != "" || $event.email != "" || $event.website != "" || $lieuPhone != "" || $lieuEmail != "" || $lieuWebsite != ""}
				<h3>{'_EVENT_CONTACT'|constant}</h3>
{if $event.phone != "" || $lieuPhone != ""}
					<b>{'_EVENT_CONTACT_PHONE'|constant} : </b><br>
					{if $event.phone != ""}{$event.phone}{else}{$lieuPhone}{/if}
{/if}
{if $event.email != "" || $lieuEmail != ""}
					<b>{'_EVENT_CONTACT_MAIL'|constant} : </b><br>
					{if $event.email != ""}{$event.email}{else}{$lieuEmail}{/if}
{/if}
{if $event.website != "" || $lieuWebsite != ""}
					<b>{'_EVENT_CONTACT_WEBSITE'|constant} :</b><br>
					{if $event.website != ""}{$event.website}{else}{$lieuWebsite}{/if}
{/if}
{/if}
		</td>
</td></tr>
</table>