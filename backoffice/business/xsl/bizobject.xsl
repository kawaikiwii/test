<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        bizobject.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

  <xsl:template match="/">
    <strong>Error: business/xsl/<xsl:value-of select="$bizclass"/>.xsl does not exist!</strong>
  </xsl:template>

</xsl:stylesheet>
