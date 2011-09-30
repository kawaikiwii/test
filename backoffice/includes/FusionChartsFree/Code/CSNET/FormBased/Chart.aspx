<%@ Page Language="C#" AutoEventWireup="true" CodeFile="Chart.aspx.cs" Inherits="FormBased_Chart" %>

<HTML>
<HEAD>
	<TITLE>	FusionCharts Free - Form Based Data Charting Example	</TITLE>
	<%
    //You need to include the following JS file, if you intend to embed the chart using JavaScript.
    //Embedding using JavaScripts avoids the "Click to Activate..." issue in Internet Explorer
    //When you make your own charts, make sure that the path to this JS file is correct. Else, you would get JavaScript errors.
	%>	
	<SCRIPT LANGUAGE="Javascript" SRC="../FusionCharts/FusionCharts.js"></SCRIPT>
	<style type="text/css">
	<!--
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	.text{
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	-->
	</style>
</HEAD>
	
<BODY>

<CENTER>
<h2><a href="http://www.fusioncharts.com" target="_blank">FusionCharts Free</a> Form-Based Data Example</h2>
<h4>Restaurant Sales Chart below</h4>

<%=CreateChart()
	
%>
<a href='javascript:history.go(-1);'>Enter data again</a>
<BR><BR>
<a href='../NoChart.html' target="_blank">Unable to see the chart above?</a>
<BR><H5 ><a href='../default.aspx'>&laquo; Back to list of examples</a></h5>
</CENTER>
</BODY>
</HTML>