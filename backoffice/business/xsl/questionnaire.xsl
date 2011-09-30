<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        questionnaire.xsl
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

  <!-- Modèle de présentation d'un questionnaire -->
  <xsl:template match="questionnaire">
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
        <td width="20" align="center">
          <img src="img/icons/questionnaire.gif" alt="" width="16" height="16" border="0" hspace="2"/>
        </td>
        <td width="100%">
          <div class="item" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('questionnaire',<xsl:value-of select="id"/>,'<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>')</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="php:function('constant', string('_BIZ_QUESTIONNAIRE'))"/> (<xsl:value-of select="id"/>) (<em><xsl:value-of select="type"/></em>): <strong>
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
          <xsl:if test="dek != ''">
            <img src="img/texte_long.gif" border="0" style="cursor:pointer" hspace="2">
              <xsl:attribute name="alt"><xsl:value-of select="php:function('constant', string('_BIZ_SHOWHIDE_DETAILS'))"/></xsl:attribute>
              <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_SHOWHIDE_DETAILS'))"/></xsl:attribute>
              <xsl:attribute name="onclick">swap('qu_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
            </img>
          </xsl:if>
        </td>
        <td>
          <div class="item">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('questionnaire',<xsl:value-of select="id"/>)</xsl:attribute>
            </xsl:if>
            <div style="margin-bottom:4px" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
              <xsl:if test="createdAt != ''">
                <xsl:value-of select="php:function('constant', string('_BIZ_CREATED_AT'))"/>: <xsl:value-of select="createdAt"/>
              </xsl:if>
              <xsl:if test="author != ''">
                <![CDATA[ ]]><xsl:value-of select="php:function('constant', string('_BIZ_BY'))"/>: <xsl:value-of select="author"/>
              </xsl:if>
            </div>
            <xsl:if test="dek != ''">
              <div style="display:none; margin: 0; padding: 0 0 8px;" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
                <xsl:attribute name="id">qu_<xsl:value-of select="id"/></xsl:attribute>
                <em><xsl:value-of select="dek" disable-output-escaping="yes" /></em>
              </div>
            </xsl:if>
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
  <!-- /Modèle de présentation d'un questionnaire -->

</xsl:stylesheet>
