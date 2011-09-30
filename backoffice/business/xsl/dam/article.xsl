<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        article.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>

  <xsl:template match="/BizObject">
    <article>
        <sourceId>
            <xsl:value-of select="Id" disable-output-escaping="yes" />
        </sourceId>
        <source>DAM</source>
        <title>
            <xsl:value-of select="Content/Metadata/Name" disable-output-escaping="yes" />
        </title>
        <abstract>
            <xsl:value-of select="Content/Metadata/Content" disable-output-escaping="yes" />
        </abstract>
    </article>
  </xsl:template>

</xsl:stylesheet>
