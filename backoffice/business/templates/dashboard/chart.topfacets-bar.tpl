<div id="chart_{$module.id}">
</div>
<script type="text/javascript">
    var xml = "<graph showAlternateVGridColor='0' pieYScale='60' showNames='1' showValues='0' showLimits='0' showBarShadow='0' numDivLines='0' canvasBorderThickness='0' canvasBorderColor='E0E0E0'>";
    {wcm name="facets" assign="facets" facet=`$params.index` max="6" sort="1"}
    {foreach from=$facets item="facet"}
        {assign var="link" value="index.php?paramPrefix=qs_&_wcmAction=business/search&_wcmTodo=initSearch&qs_query=`$facet.name`:`$facet.value`"}
        xml += "<set value='{$facet.count}' name='{$facet.text|lower|capitalize|truncate:20:"..."}' hoverText='{$facet.text|lower|capitalize}'";
        xml += " link='{$config.wcm.backOffice.url|urlencode}{$link|urlencode}'";
        xml += " color='{cycle values="D00000,E06060,F08080,A0A0A0,B0B0B0,C0C0C0,D0D0D0"}'/>";
    {/foreach}
    xml += "</graph>";

    var chart = new FusionCharts(wcmBaseURL + "/includes/FusionChartsFree/FCF_Pie3D.swf",
                                 "chart_{$module.id}", "320", "160", "0", "0");
    chart.setDataXML(xml);
    chart.render("chart_{$module.id}");
</script>