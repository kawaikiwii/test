<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:template match="/">
		
		<photo>
			<title>
				<xsl:value-of select="//Name" disable-output-escaping="yes" />
			</title>
			<caption>
				<xsl:value-of select="//Description" disable-output-escaping="yes" />
			</caption>
			<source>
				<xsl:value-of select="//Source" disable-output-escaping="yes" />
			</source>
			<sourceId>
				<xsl:value-of select="//SourecID" disable-output-escaping="yes" />
			</sourceId>
			<width>
				<xsl:value-of select="//Metadata/MediaProperties/MediaTypeProperties/Width" disable-output-escaping="yes" />
			</width>
			<height>
				<xsl:value-of select="//Metadata/MediaProperties/MediaTypeProperties/Height" disable-output-escaping="yes" />
			</height>
			<original>
				<xsl:value-of select="//Metadata/MediaProperties/FileProperties/OriginalName" disable-output-escaping="yes" />
			</original>
			<publicationDate>
				<xsl:value-of select="//Content/Metadata/PublicationDate" disalbe-output-escaping="yes" />
			</publicationDate>
			<createdAt>
				<xsl:value-of select="//CreatedAt" disable-output-escaping="yes" />
			</createdAt>
		</photo>
		
	</xsl:template>
</xsl:stylesheet>