{capture name="MAIN"}
<div class="ari-desk-content ari-desk-left">
	<div class="ari-desk-container">
		<h2 id="forecast-previstar" class="ari-desk-container-title">Prévistars</h2>
		<ul class="ari-desk-container-list">
			<li class="ari-container-title">Toute l'actualité des personnalités, mois par mois</li>
{foreach from=`$PREVISTARS` item=item}
			<li class="ari-item" onclick="ARe.pview('forecast', {$item->id})"><span class="ari-title">{$item->title}</span></li>
{/foreach}
		</ul>
	</div>
</div>
{/capture}
{assign var="filename" value="`$config.wcm.webSite.repository`sites/`$site->code`/homes/forecast.right.html"}
{dump file=$filename content=$smarty.capture.MAIN utf8=true}