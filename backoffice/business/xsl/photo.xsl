<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        photo.xsl
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
 
  <!-- Template for rendering a photo -->
  <xsl:template match="photo">
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
        <td width="114" height="114" align="center" bgcolor="#000000">
          <div style="width:114px; padding:2px; cursor:pointer">
            <xsl:attribute name="onclick">openDialog( 'popup.php', 'module=photo_previsualisation&amp;l=<xsl:value-of select="width"/>&amp;h=<xsl:value-of select="height"/>&amp;source=<xsl:value-of select="original"/>', <xsl:value-of select="width+100"/>, <xsl:value-of select="height + 120"/>,null,null,'photo');</xsl:attribute>
            <xsl:if test="thumbnail != ''">
            <img border="0">
              <xsl:attribute name="src">
                <xsl:value-of select="thumbnail"/>
              </xsl:attribute>
              <xsl:attribute name="alt">
                <xsl:value-of select="php:function('constant', string('_BIZ_PHOTOS_SEE_ORIGINAL'))"/>
              </xsl:attribute>
              <xsl:choose>
                <xsl:when test="tumbWidth &gt; thumbHeight">
                  <xsl:if test="tumbWidth &gt; 110">
                    <xsl:attribute name="width">110</xsl:attribute>
                  </xsl:if>
                </xsl:when>
                <xsl:otherwise>
                  <xsl:if test="thumbHeight &gt; 110">
                    <xsl:attribute name="height">110</xsl:attribute>
                  </xsl:if>
                </xsl:otherwise>
              </xsl:choose>
            </img>
            </xsl:if>
          </div>
        </td>
        <td width="98%" valign="top">
          <div class="item" style="margin:8px" onmouseover="this.className='itemOver';" onmouseout="this.className='item';">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('photo',<xsl:value-of select="id"/>,'<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>' )</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="php:function('constant', string('_BIZ_PHOTO'))"/> (<xsl:value-of select="id"/>): <strong>
              <xsl:apply-templates select="title"/>
            </strong>
            <xsl:if test="$workflowState != ''">
              (<xsl:value-of select="$workflowState"/>)
            </xsl:if>
            <xsl:if test="createdAt != ''">
              <br/><xsl:value-of select="php:function('constant', string('_BIZ_CREATED_AT'))"/>: <xsl:value-of select="createdAt"/>
            </xsl:if>
            <xsl:if test="credits != ''">
              <br/><xsl:value-of select="php:function('constant', string('_BIZ_CREDITS'))"/>: <xsl:value-of select="credits"/>
            </xsl:if>
            <xsl:if test="caption != ''">
              <br/>
              <em>
                <xsl:value-of select="caption" disable-output-escaping="yes" />
              </em>
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
  <!-- /Template for rendering a photo -->

</xsl:stylesheet>
