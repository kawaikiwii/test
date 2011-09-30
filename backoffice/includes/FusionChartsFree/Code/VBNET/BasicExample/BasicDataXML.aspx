<%@ Page Language="VB" AutoEventWireup="false" CodeFile="BasicDataXML.aspx.vb" Inherits="BasicExample_BasicDataXML" %>

<HTML>
<HEAD>
	<TITLE>
	FusionCharts Free - Simple Column 3D Chart using dataXML method
	</TITLE>	
	<style type="text/css">
	<!--
	body {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	-->
	</style>
</HEAD>
<BODY>

<CENTER>
<h2><a href="http://www.fusioncharts.com" target="_blank">FusionCharts Free</a> Examples</h2>
<h4>Basic example using dataXML method (with XML data hard-coded in ASP.Net page itself)</h4>
<p>If you view the source of this page, you'll see that the XML data is present in this same page (inside HTML code). We're not calling any external XML (or script) files to serve XML data. dataXML method is ideal when you've to plot small amounts of data.</p>
<%=CreateCharts()

%>
<BR><BR>
<a href='../NoChart.html' target="_blank">Unable to see the chart above?</a>
<BR><H5 ><a href="../Default.aspx">&laquo; Back to list of examples</a></h5>
</CENTER>
</BODY>
</HTML>
