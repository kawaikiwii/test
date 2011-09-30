<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
  
  
		<xsl:template match="/">
		
			<article>
				<source>
					<xsl:value-of select="//Content/Metadata/Source" disable-output-escaping="yes" />
				</source>
				<sourceId>
					<xsl:value-of select="//Content/Metadata/SourceID" disable-output-escaping="yes" />
				</sourceId>
				<publicationDate>
					<xsl:value-of select="//Content/Metadata/Publicationdate" disable-output-escaping="yes" />
				</publicationDate>
				<chapters>
				    <item>
				        <text>
					       <xsl:value-of select="//Content/Metadata/Content" disable-output-escaping="yes" />
				        </text>
				    </item>
				</chapters>
				<title>
					<xsl:value-of select="//Content/Metadata/Title" disable-output-escaping="yes" />
				</title>
				<abstract>
					<xsl:value-of select="//Content/Metadata/Abstract" disable-output-escaping="yes" />
				</abstract>
				<author>
					<xsl:value-of select="//Content/Metadata/Author" disable-output-escaping="yes" />
				</author>
				<credits>
					<xsl:value-of select="//Content/Metadata/Credits" disable-output-escaping="yes" />
				</credits>
			</article>
		
		</xsl:template>
</xsl:stylesheet>