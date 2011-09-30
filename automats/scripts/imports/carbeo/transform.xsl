<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
	<xsl:output method="html" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" standalone="yes" version="4.0"/>
	
	<xsl:template match="/">
		<table border="0" cellpadding="1" cellspacing="2" width="601">
			<colgroup>
				<col/>
				<col width="100"/>
				<col width="100"/>
				<col width="100"/>
				<col width="100"/>
				<col width="100"/>
			</colgroup>
			<tr align="left" valign="top">
				<td colspan="6">
					<strong>Barom&#232;tre national hebdomadaire du prix des carburants Relaxfil/Carbeo.com</strong>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td> </td>
				<td><strong>Prix moyen<br/>en euro (J-7)</strong></td>
				<td><strong>Prix moyen<br/>en euro (J-1)</strong></td>
				<td align="center" valign="top"><strong>Tendance</strong></td>
				<td><strong>Prix mini<br/>en euro</strong></td>
				<td><strong>Prix maxi<br/>en euro</strong></td>
			</tr>
			<tr align="left" valign="top">
				<td>SP98</td>
				<td>
					<xsl:value-of select="//sp98/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//sp98/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//sp98/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//sp98/min"/>
				</td>
				<td>
					<xsl:value-of select="//sp98/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>SP95</td>
				<td>
					<xsl:value-of select="//sp95/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//sp95/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//sp95/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//sp95/min"/>
				</td>
				<td>
					<xsl:value-of select="//sp95/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>SP95-E10</td>
				<td>
					<xsl:value-of select="//e10/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//e10/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//e10/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//e10/min"/>
				</td>
				<td>
					<xsl:value-of select="//e10/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>Gasoil</td>
				<td>
					<xsl:value-of select="//go/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//go/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//go/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//go/min"/>
				</td>
				<td>
					<xsl:value-of select="//go/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>Gasoil Plus</td>
				<td>
					<xsl:value-of select="//goPlus/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//goPlus/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//goPlus/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//goPlus/min"/>
				</td>
				<td>
					<xsl:value-of select="//goPlus/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>GPL</td>
				<td>
					<xsl:value-of select="//gpl/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//gpl/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//gpl/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//gpl/min"/>
				</td>
				<td>
					<xsl:value-of select="//gpl/max"/>
				</td>
			</tr>
			<tr align="left" valign="top">
				<td>E85</td>
				<td>
					<xsl:value-of select="//e85/moyj7"/>
				</td>
				<td>
					<xsl:value-of select="//e85/moyj1"/>
				</td>
				<td align="center" valign="top">
					<xsl:variable name="tendance">
						<xsl:value-of select="number(translate(//e85/tendance, '%', ''))"/>
					</xsl:variable>
					<img>
						<xsl:choose>
							<xsl:when test="$tendance = 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-stable.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:when test="$tendance &lt; 0">
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-baisse.gif')"/></xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="src"><xsl:value-of select="concat($urlRepository, 'images/import/news/carbeo/prix-hausse.gif')"/></xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
					</img>
				</td>
				<td>
					<xsl:value-of select="//e85/min"/>
				</td>
				<td>
					<xsl:value-of select="//e85/max"/>
				</td>
			</tr>
			<tr>
				<td colspan="6"> </td>
			</tr>
			<tr>
				<td colspan="6">Méthodologie : Le Baromètre national hebdomadaire du prix des carburants Relaxfil/Carbeo.com est réalisé à partir des données du site Carbeo.com. Ce site communautaire collecte les prix des carburants sur toute la France. Des automobilistes relèvent les prix dans les stations services et les mettent à disposition afin que chacun puisse trouver le carburant le moins cher. Tous les jours, des milliers de prix font l'objet d'une actualisation qui permet de calculer les prix moyens pour chacun des carburants et détermine la tendance sur les sept derniers jours.</td>
			</tr>
			<tr>
				<td colspan="6">Site : <a target="_blank" href="http://www.carbeo.com">www.carbeo.com</a></td>
			</tr>
		</table>
	</xsl:template>
</xsl:stylesheet>
