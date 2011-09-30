<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" exclude-result-prefixes="xsl">
	<xsl:output method="html" version="1.0" standalone="yes" omit-xml-declaration="yes" encoding="UTF-8" media-type="string" indent="yes"/>

	<xsl:template match="/">
		<p>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="55%">
						<table border="1" cellpadding="0" cellspacing="0" width="392">
							<thead>
								<tr>
									<th width="40%">Ville</th>
									<th width="30%">En moyenne hier</th>
									<th width="30%">Relevé ce matin</th>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="//city">
									<xsl:sort data-type="text" select="@name"/>
									<xsl:variable name="town" select="./@id" />
									<xsl:if test="./index[@type='background']/dayAverage/value != 'None' and ./index[@type='background']/currentSituation/value != 'None'">
										<tr>
											<td>
												<div style="height:32px; display:table-cell; vertical-align:middle;">
													<xsl:if test="$town='paris' or $town='clermont' or $town='reims' or $town='strasbourg' or $town='rouen' or $town='lehavre' or $town='toulouse'">
														<!-- xsl:attribute name="style">color:#0000ff; height:32px; display:table-cell; vertical-align:middle;</xsl:attribute -->
													</xsl:if>
													<xsl:value-of select="./@name" disable-output-escaping="yes" />
												</div>
											</td>
											<td>
												<xsl:choose>
													<xsl:when test="./index[@type='background']/dayAverage/value &gt;= 0 and ./index[@type='background']/dayAverage/value &lt; 26">
														<xsl:attribute name="style">text-align:center;background-color:#79bc6a</xsl:attribute>
													</xsl:when>
													<xsl:when test="./index[@type='background']/dayAverage/value &gt; 25 and ./index[@type='background']/dayAverage/value &lt; 51">
														<xsl:attribute name="style">text-align:center;background-color:#bbcf4c</xsl:attribute>
													</xsl:when>
													<xsl:when test="./index[@type='background']/dayAverage/value &gt; 50 and ./index[@type='background']/dayAverage/value &lt; 76">
														<xsl:attribute name="style">text-align:center;background-color:#eec20b</xsl:attribute>
													</xsl:when>
													<xsl:when test="./index[@type='background']/dayAverage/value &gt; 75 and ./index[@type='background']/dayAverage/value &lt; 101">
														<xsl:attribute name="style">text-align:center;background-color:#f29305</xsl:attribute>
													</xsl:when>
													<xsl:when test="./index[@type='background']/dayAverage/value &gt; 100">
														<xsl:attribute name="style">text-align:center;background-color:#e8416f</xsl:attribute>
													</xsl:when>
													<xsl:otherwise>
														<xsl:attribute name="style">text-align:center</xsl:attribute>
													</xsl:otherwise>
												</xsl:choose>
												<xsl:value-of select="./index[@type='background']/dayAverage/value"/>
											</td>
											<td>
												<xsl:if test="./index[@type='background']/currentSituation">
													<xsl:choose>
														<xsl:when test="./index[@type='background']/currentSituation/value &gt;= 0 and ./index[@type='background']/currentSituation/value &lt; 26">
															<xsl:attribute name="style">text-align:center;background-color:#79bc6a</xsl:attribute>
														</xsl:when>
														<xsl:when test="./index[@type='background']/currentSituation/value &gt; 25 and ./index[@type='background']/currentSituation/value &lt; 51">
															<xsl:attribute name="style">text-align:center;background-color:#bbcf4c</xsl:attribute>
														</xsl:when>
														<xsl:when test="./index[@type='background']/currentSituation/value &gt; 50 and ./index[@type='background']/currentSituation/value &lt; 76">
															<xsl:attribute name="style">text-align:center;background-color:#eec20b</xsl:attribute>
														</xsl:when>
														<xsl:when test="./index[@type='background']/currentSituation/value &gt; 75 and ./index[@type='background']/currentSituation/value &lt; 101">
															<xsl:attribute name="style">text-align:center;background-color:#f29305</xsl:attribute>
														</xsl:when>
														<xsl:when test="./index[@type='background']/currentSituation/value &gt; 100">
															<xsl:attribute name="style">text-align:center;background-color:#e8416f</xsl:attribute>
														</xsl:when>
														<xsl:otherwise>
															<xsl:attribute name="style">text-align:center</xsl:attribute>
														</xsl:otherwise>
													</xsl:choose>
													<xsl:value-of select="./index[@type='background']/currentSituation/value"/>
												</xsl:if>
											</td>
										</tr>
									</xsl:if>
								</xsl:for-each>
							</tbody>
						</table>
					</td>
					<td width="45%" align="center" valign="middle">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td><span class="titreTableauSituation"><strong><xsl:text disable-output-escaping="yes">L&#233;gende</xsl:text></strong></span></td>
							</tr>
							<tr>
								<td>
									<table width="100%" border="1" cellpadding="0" cellspacing="0" class="tableauIndices">
										<thead>
											<tr>
												<th id="th0BD798D80000"><xsl:text disable-output-escaping="yes">Qualit&#233; de l'air</xsl:text></th>
												<th id="th0BD798D80001" colspan="2"><xsl:text disable-output-escaping="yes">Indice</xsl:text></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td headers="th0BD798D80000"><xsl:text disable-output-escaping="yes">Tr&#232;s bon</xsl:text></td>
												<td headers="th0BD798D80001" width="15" style="background-color:#79bc6a"> </td>
												<td headers="th0BD798D80001" align="center">0 / 25</td>
											</tr>
											<tr>
												<td headers="th0BD798D80000"><xsl:text disable-output-escaping="yes">Bon</xsl:text></td>
												<td headers="th0BD798D80001" width="15" style="background-color:#bbcf4c"> </td>
												<td headers="th0BD798D80001" align="center">26 / 50</td>
											</tr>
											<tr>
												<td headers="th0BD798D80000"><xsl:text disable-output-escaping="yes">Moyen</xsl:text></td>
												<td headers="th0BD798D80001" width="15" style="background-color:#eec20b"> </td>
												<td headers="th0BD798D80001" align="center">51 / 75</td>
											</tr>
											<tr>
												<td headers="th0BD798D80000"><xsl:text disable-output-escaping="yes">Mauvais</xsl:text></td>
												<td headers="th0BD798D80001" width="15" style="background-color:#f29305"> </td>
												<td headers="th0BD798D80001" align="center">76 / 100</td>
											</tr>
											<tr>
												<td headers="th0BD798D80000"><xsl:text disable-output-escaping="yes">Tr&#232;s mauvais</xsl:text></td>
												<td headers="th0BD798D80001" width="15" style="background-color:#e8416f"> </td>
												<td headers="th0BD798D80001" align="center">&gt; 100</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2"> </td>
				</tr>
				<tr>
					<td colspan="2">
						<xsl:text disable-output-escaping="yes">M&#233;thodologie : Ce barom&#232;tre est &#233;tabli &#224; partir de l'indice CiteAIR (Common information to European air), du lundi au vendredi &#224; 10h30. Cet indice apporte, entre autres, une information comparable sur la qualit&#233; de l'air des villes &#224; l'&#233;chelle europ&#233;enne en temps r&#233;el, sur la pollution ambiante. D&#233;velopp&#233; dans le cadre du projet europ&#233;en du m&#234;me nom, il prend en compte les polluants les plus probl&#233;matiques dans les villes europ&#233;ennes, dont le dioxyde d'azote, l'ozone et les particules.</xsl:text>
					</td>
				</tr>
				<tr>
					<td colspan="2"> </td>
				</tr>
				<tr>
					<td colspan="2">Site : <a href="http://www.airqualitynow.eu" target="_blank">www.airqualitynow.eu</a></td>
				</tr>
			</table>
		</p>
	</xsl:template>

</xsl:stylesheet>
