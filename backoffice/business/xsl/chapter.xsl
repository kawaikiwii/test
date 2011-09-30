<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        chapter.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">

  <xsl:param name="callback" select="''"/>

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>

  <!--
      Template for chapters.
      This bizobject is never shown outside of a article (ie. it cannot be searched through a bizsearch)
  -->
  <xsl:template match="chapter">
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
        <td width="20" align="center">
          <img src="img/icons/article.gif" alt="" width="16" height="16" border="0" hspace="2"/>
        </td>
        <td width="100%">
          <div class="item">
              <xsl:attribute name="onclick">swap('ar_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
            <strong>
              <xsl:apply-templates select="title"/>
            </strong>
          </div>
        </td>
      </tr>
      <tr valign="top">
        <td align="center">
        </td>
        <td>
          <div class="item">
              <xsl:attribute name="onclick">swap('ar_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
          </div>
        </td>
      </tr>
      <tr valign="top">
        <td align="center">
            <img src="img/texte_long.gif" border="0" style="cursor:pointer" hspace="2">
              <xsl:attribute name="onclick">swap('ar_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
              <xsl:attribute name="alt"><xsl:value-of select="php:function('constant', string('_BIZ_SHOWHIDE_DETAILS'))"/></xsl:attribute>
              <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_SHOWHIDE_DETAILS'))"/></xsl:attribute>
            </img>
        </td>
        <td>
          <div class="item">
            <div style="margin-bottom:4px" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
              <xsl:attribute name="onclick">swap('ar_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
              <xsl:value-of select="php:function('constant', string('_BIZ_CREATED_AT'))"/>: <xsl:value-of select="createdAt"/>
            </div>
              <div style="display:none; margin: 0; padding: 0 0 8px;" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
                <xsl:attribute name="id">ar_<xsl:value-of select="id"/></xsl:attribute>
                <xsl:attribute name="onclick">swap('ar_<xsl:value-of select="id"/>', this, 'img/texte_long.gif', 'img/texte_court.gif');</xsl:attribute>
                <em>
                  <xsl:value-of select="text" disable-output-escaping="yes" />
                </em>
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

</xsl:stylesheet>
