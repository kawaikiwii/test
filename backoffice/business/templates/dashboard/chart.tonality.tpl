<div id="chart_{$module.id}">
</div>
<script type="text/javascript">
    var xml = "<graph yAxisMaxValue='100' numdivlines='0' showColumnShadow='0' showLimits='0' showValues='0' showLegend='0' canvasBorderColor='EEEEEE' canvasBorderThickness='0' animation='1' numberSuffix='%25' decimalPrecision='1'>";
    xml += "<categories>";
    xml += "<category name='Articles'/>";
    xml += "<category name='Comments'/>";
    xml += "</categories>";
    xml += "<dataset seriesname='Negative' color='F04040' alpha='100' showValues='1'>";
    xml += "<set value='25' />";
    xml += "<set value='55' />";
    xml += "</dataset>";
    xml += "<dataset seriesname='Neutral' color='C0C0C0' alpha='100' showValues='1'>";
    xml += "<set value='55' />";
    xml += "<set value='10' />";
    xml += "</dataset>";
    xml += "<dataset seriesname='Positive' color='60E060' alpha='100' showValues='1'>";
    xml += "<set value='20' />";
    xml += "<set value='35' />";
    xml += "</dataset>";
    xml += "</graph>";
    
    var chart = new FusionCharts(wcmBaseURL + "/includes/FusionChartsFree/FCF_MSColumn2D.swf",
                                 "chart_{$module.id}", "320", "160", "0", "0");
    chart.setDataXML(xml);
    chart.render("chart_{$module.id}");
</script>