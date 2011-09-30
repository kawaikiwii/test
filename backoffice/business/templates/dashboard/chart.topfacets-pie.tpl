<div id="chart_{$module.id}">
</div>
<script type="text/javascript">
    var xml = "<graph caption='' bgColor='ffffff' decimalPrecision='0' showPercentageValues='0' showNames='1' showValues='0' showShadow='0' pieBorderColor='F8F8F8'>";
    {wcm name="facets" assign="facets" facet=`$params.index` max="7" sort="1"}
    {foreach from=$facets item="facet"}
        {assign var="link" value="index.php?paramPrefix=qs_&_wcmAction=business/search&_wcmTodo=initSearch&qs_query=`$facet.name`:`$facet.value`"}
        xml += "<set value='{$facet.count}' name='{$facet.text|lower|capitalize|truncate:20:"..."}' hoverText='{$facet.text|lower|capitalize}'";
        xml += " link='{$config.wcm.backOffice.url|urlencode}{$link|urlencode}'";
        xml += " color='{cycle values="D00000,E06060,F08080,A0A0A0,B0B0B0,C0C0C0,D0D0D0"}'/>";
    {/foreach}
    xml += "</graph>";

    var chart = new FusionCharts(wcmBaseURL + "/includes/FusionChartsFree/FCF_Doughnut2D.swf",
                                 "chart_{$module.id}", "320", "160", "0", "0");
    chart.setDataXML(xml);
    chart.render("chart_{$module.id}");
</script>