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
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
          <td width="100%">
            <strong><xsl:value-of select="Name"/></strong>
          </td>
      </tr>
     </table>
  </xsl:template>
</xsl:stylesheet>
