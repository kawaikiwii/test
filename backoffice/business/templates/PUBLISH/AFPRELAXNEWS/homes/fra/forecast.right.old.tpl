{capture name="MAIN"}
{assign var="today" value=$smarty.now|date_format:"%Y-%m-%d"}
{assign var="toweek" value="+ 1 week"|strtotime:$smarty.now}
{assign var="tomonth" value="+ 1 month"|strtotime:$smarty.now}
{assign var="toyear" value="+ 1 year"|strtotime:$smarty.now}
{assign var="toweek" value=$toweek|date_format:"%Y-%m-%d"}
{assign var="tomonth" value=$tomonth|date_format:"%Y-%m-%d"}
{assign var="toyear" value=$toyear|date_format:"%Y-%m-%d"}
<div class="ari-desk-content ari-desk-right">
	<div class="ari-desk-container" >
		<h2 id="forecast-sport" class="ari-desk-container-title" onclick="ARe.search.rubric('Sport', 'forecast' ,'(213,217,248)')">Sport</h2>
		<ul class="ari-desk-container-list right">		
		<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Sport', {literal}{{/literal}classname:'forecast',rubric:'(213,217,248)', forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les 7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Sport', {literal}{{/literal}classname:'forecast',rubric:'(213,217,248)', forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Sport', {literal}{{/literal}classname:'forecast',rubric:'(213,217,248)', forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Sport', 'forecast' ,'(213,217,248)')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left" >	
		<li class="ari-container-title">A Noter</li>	
{foreach from=$BOX1 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
{/foreach}
		</ul>
	</div>
	<div class="ari-desk-container" >
		<h2 id="forecast-tourism" class="ari-desk-container-title" onclick="ARe.search.rubric('Tourisme', 'forecast' ,'(238,239,240,241,242,243,244,245)')">Tourisme</h2>
		<ul class="ari-desk-container-list right" >		
		<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Tourisme', {literal}{{/literal}classname:'forecast',rubric:'(238,239,240,241,242,243,244,245)', forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les 7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Tourisme', {literal}{{/literal}classname:'forecast',rubric:'(238,239,240,241,242,243,244,245)', forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Tourisme', {literal}{{/literal}classname:'forecast',rubric:'(238,239,240,241,242,243,244,245)', forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Tourisme', 'forecast' ,'(238,239,240,241,242,243,244,245)')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left">	
		<li class="ari-container-title">A Noter</li>	
{foreach from=$BOX2 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
{/foreach}
		</ul>
	</div>
		<div class="ari-desk-container" >
		<h2 id="forecast-video" class="ari-desk-container-title" onclick="ARe.search.rubric('Vidéos', 'forecast' ,'210')">Vidéos</h2>
		<ul class="ari-desk-container-list right">		
		<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Vidéos', {literal}{{/literal}classname:'forecast',rubric:210, forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les 7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Vidéos', {literal}{{/literal}classname:'forecast',rubric:210, forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Vidéos', {literal}{{/literal}classname:'forecast',rubric:210, forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Vidéos', 'forecast' ,'210')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left" >	
		<li class="ari-container-title">A Noter</li>	
{foreach from=$BOX3 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
{/foreach}
		</ul>
	</div>
</div>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/forecast.right.html"}
{dump file=$filename content=$smarty.capture.MAIN utf8=true}