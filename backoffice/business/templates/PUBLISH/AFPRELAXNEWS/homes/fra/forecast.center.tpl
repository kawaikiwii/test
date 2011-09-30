{capture name="MAIN"}
{assign var="today" value=$smarty.now|date_format:"%Y-%m-%d"}
{assign var="toweek" value="+ 1 week"|strtotime:$smarty.now}
{assign var="tomonth" value="+ 1 month"|strtotime:$smarty.now}
{assign var="toyear" value="+ 1 year"|strtotime:$smarty.now}
{assign var="toweek" value=$toweek|date_format:"%Y-%m-%d"}
{assign var="tomonth" value=$tomonth|date_format:"%Y-%m-%d"}
{assign var="toyear" value=$toyear|date_format:"%Y-%m-%d"}
<div class="ari-desk-content ari-desk-center">
	<div class="ari-desk-container">
		<h2 id="forecast-cinema" class="ari-desk-container-title" onclick="ARe.search.rubric('Cinéma', 'forecast' ,'197')">Cinéma</h2>
		<ul class="ari-desk-container-list right">	
			<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Cinéma', {literal}{{/literal}classname:'forecast',rubric:197, forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les 7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Cinéma', {literal}{{/literal}classname:'forecast',rubric:197, forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Cinéma', {literal}{{/literal}classname:'forecast',rubric:197, forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Cinéma', 'forecast' ,'197')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left">	
		<li class="ari-container-title">A Noter</li>		
{foreach from=$BOX1 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
			{/foreach}
		</ul>
	</div>
	<div class="ari-desk-container">
		<h2 id="forecast-music" class="ari-desk-container-title" onclick="ARe.search.rubric('Musique', 'forecast' ,'196')">Musique</h2>
		<ul class="ari-desk-container-list right">		
			<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Musique', {literal}{{/literal}classname:'forecast',rubric:196, forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Musique', {literal}{{/literal}classname:'forecast',rubric:196, forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Musique', {literal}{{/literal}classname:'forecast',rubric:196, forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Musique', 'forecast' ,'196')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left">		
			<li class="ari-container-title">A Noter</li>		
{foreach from=$BOX2 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
{/foreach}
		</ul>
	</div>
		<div class="ari-desk-container" >
		<h2 id="forecast-book" class="ari-desk-container-title" onclick="ARe.search.rubric('Livres', 'forecast' ,'(202,203,204)')">Livres</h2>
		<ul class="ari-desk-container-list right" >		
			<li class="ari-container-title">Rechercher Sur</li>	
			<li class="ari-item" onclick="ARe.search.f('Livres', {literal}{{/literal}classname:'forecast',rubric:'(202,203,204)', forecastStartDate:'{$today}', forecastEndDate:'{$toweek}'{literal}}{/literal})"><span class="ari-title">Les 7 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Livres', {literal}{{/literal}classname:'forecast',rubric:'(202,203,204)', forecastStartDate:'{$today}', forecastEndDate:'{$tomonth}'{literal}}{/literal})"><span class="ari-title">Les 30 prochains jours</span></li>
			<li class="ari-item" onclick="ARe.search.f('Livres', {literal}{{/literal}classname:'forecast',rubric:'(202,203,204)', forecastStartDate:'{$today}', forecastEndDate:'{$toyear}'{literal}}{/literal})"><span class="ari-title">Les prochains mois</span></li>
			<li class="ari-item" onclick="ARe.search.rubric('Livres', 'forecast' ,'(202,203,204)')"><span class="ari-title">Tout</span></li>
		</ul>
		<ul class="ari-desk-container-list left">		
			<li class="ari-container-title">A Noter</li>		
{foreach from=$BOX3 item=item}
				<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title|truncate:40:"..."}</span></li>
{/foreach}
		</ul>
	</div>
</div>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/forecast.center.html"}
{dump file=$filename content=$smarty.capture.MAIN utf8=true}