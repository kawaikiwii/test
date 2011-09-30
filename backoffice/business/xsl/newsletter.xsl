<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        newsletter.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">

  <xsl:param name="callback" select="''"/>
  <xsl:param name="workflowState" select="''"/>

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>

  <!-- Template for rendering newsletter -->
  <xsl:template match="newsletter">
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
        <td width="20" align="center">
          <img src="img/icons/newsletter.gif" alt="" width="16" height="16" border="0" hspace="2">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('newsletter',<xsl:value-of select="id"/>, '<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>')</xsl:attribute>
            </xsl:if>
           </img>
        </td>
        <td width="100%">
          <div class="item" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick">
                <xsl:value-of select="$callback"/>('newsletter',<xsl:value-of select="id"/>,'<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>')
              </xsl:attribute>
            </xsl:if>
            <xsl:value-of select="php:function('constant', string('_BIZ_NEWSLETTER'))"/> (<xsl:value-of select="id"/>): <strong>
              <xsl:apply-templates select="title"/>
            </strong>
            <xsl:if test="$workflowState != ''">
              (<xsl:value-of select="$workflowState"/>)
            </xsl:if>
          </div>
        </td>
      </tr>
      <tr valign="top">
        <td align="center">
        </td>
        <td>
          <div class="item">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('newsletter',<xsl:value-of select="id"/>,'<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>')</xsl:attribute>
            </xsl:if>
            <div style="margin-bottom:4px" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
              <xsl:if test="createdAt != ''">
                <xsl:value-of select="php:function('constant', string('_BIZ_CREATED_AT'))"/>: <xsl:value-of select="createdAt"/>
              </xsl:if>
              <xsl:if test="sender != ''">
                <![CDATA[ ]]><xsl:value-of select="php:function('constant', string('_BIZ_BY'))"/>: <xsl:value-of select="sender"/>
              </xsl:if>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </xsl:template>
  <xsl:template name="formatText">
    <xsl:param name="text"/>
    <xsl:variable name="apos">
        <xsl:text>'</xsl:text>
    </xsl:variable>
    <xsl:value-of select="php:function('str_replace', $apos, '&amp;apos;', string($text))" />
  </xsl:template>

  <!-- /Template for rendering newsletter -->

</xsl:stylesheet>
