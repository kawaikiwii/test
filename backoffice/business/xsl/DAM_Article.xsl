<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        DAM_article.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>

  <xsl:template match="/BizObject">     
    <table cellspacing="0" cellpadding="0" border="0" width="98%">
      <tr>
         <td width="20px" align="center" valign="top">
             <img src="img/icons/article.gif" alt="" width="16" height="16" border="0" hspace="2"></img>
          </td>
          <td>
            <strong><xsl:value-of select="Content/Metadata/Title"/></strong>
          </td>
          <td width="70px" align="right">
                <a style='cursor:pointer'>
                    <xsl:attribute name="onClick">manageDamObject('<xsl:value-of select="//BizObject/@Class"/>',<xsl:value-of select="Id"/>,'DAMObjectManagerInterface','manage')</xsl:attribute>
                    Importer
                </a>
          </td>
          <td width="20px">
                <a style='cursor:pointer'>
                    <xsl:attribute name="onClick">manageDamObject('<xsl:value-of select="//BizObject/@Class"/>',<xsl:value-of select="Id"/>,'DAMObjectManagerInterface','manage')</xsl:attribute>
                    <img src="img/arrow_right.gif" alt="" width="16" height="16" border="0" hspace="2"></img>
                </a>
          </td>
      </tr>
     </table>
  </xsl:template>
</xsl:stylesheet>
