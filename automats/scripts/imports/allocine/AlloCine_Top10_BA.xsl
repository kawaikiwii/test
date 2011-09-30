<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" version="1.0" standalone="yes" omit-xml-declaration="yes" encoding="UTF-8" media-type="string" indent="yes"/>

	<xsl:template match="/">
		<xsl:apply-templates select="./top"/>
	</xsl:template>
	
	<xsl:template match="top">
		<p><br /></p><table border="1" cellpadding="0" cellspacing="0" summary="Allociné" width="97%" class="AudienceTv9"><thead><tr><th id="th051546400000" style="font-size:9pt">Rang</th><th id="th051546400001" style="font-size:9pt">Titre</th><th id="th051546400002" style="font-size:9pt">Sortie</th><th id="th051546400003" style="font-size:9pt">Consultations</th><th id="th051546400004" style="font-size:9pt">Semaine précédente</th></tr></thead><tbody><xsl:apply-templates select="film" mode="tplFilm"><xsl:sort data-type="number" select="position"/></xsl:apply-templates></tbody></table><p><br />Méthodologie : Ce top 10 des bandes-annonces est établi chaque jeudi à partir des données du site AlloCiné. Le classement se base sur le nombre de visionnages réalisés par les internautes sur les bandes-annonces de tous les films en salles actuellement. Le décompte s'effectue sur les sept derniers jours, c'est-à-dire du jeudi au mercredi (sur un total de <xsl:value-of select="totaldiffusions" disable-output-escaping="yes"/> visionnages cette semaine, <xsl:value-of select="totaldiffusionsprecedent" disable-output-escaping="yes"/> la semaine précédente).</p>
	</xsl:template>
	
	<xsl:template match="film" mode="tplFilm">
		<tr align="left" valign="top"><td id="th051546400000" align="center"><xsl:value-of select="position" disable-output-escaping="yes"/></td><td id="th051546400001"><em><xsl:value-of select="titre" disable-output-escaping="yes"/></em></td><td id="th051546400002"><xsl:value-of select="datesortie" disable-output-escaping="yes"/></td><td id="th051546400003"><xsl:value-of select="nbdiffusions" disable-output-escaping="yes"/></td><td id="th051546400004"><xsl:value-of select="nbdiffusionsprecedent" disable-output-escaping="yes"/></td></tr>
	</xsl:template>

</xsl:stylesheet>
