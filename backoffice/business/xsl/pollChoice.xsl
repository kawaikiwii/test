<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        poll_choice.xsl
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
      Template for poll choices.
      This bizobject is never shown outside of a poll (ie. it cannot be searched through a bizsearch)
  -->
  <xsl:template match="pollChoice">
    <table cellspacing="0" cellpadding="0" border="0" width="90%">
      <tr>
        <td width="20" align="center">
          <img src="img/icons/poll.gif" alt="" width="16" height="16" border="0" hspace="2"/>
        </td>
        <td width="100%">
          <div class="item">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('show','<xsl:value-of select="id"/>', 'pollChoice', '<xsl:value-of select="pollId"/>', 'poll')</xsl:attribute>
            </xsl:if>
            <strong>
              <xsl:value-of select="text"  disable-output-escaping="yes"/>
            </strong>
            <xsl:if test="$totalCount != ''">
            <em>
            [<xsl:value-of select="votesCount"  disable-output-escaping="yes"/>/<xsl:value-of select="$totalCount"  disable-output-escaping="yes"/>]
            </em>
            </xsl:if>
          </div>
        </td>
      </tr>
      <xsl:if test="$totalCount != ''">
      <tr>
        <td width="20" align="center">
          <img src="img/icons/percent.gif" alt="" width="16" height="16" border="0" hspace="2"/>
        </td>
        <td width="100%">
          <div class="item">
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('show','<xsl:value-of select="id"/>', 'pollChoice', '<xsl:value-of select="pollId"/>', 'poll')</xsl:attribute>
            </xsl:if>
            <table cellspacing="0" cellpadding="0" border="0">
            <tr>
            <td width="200px" background="img/icons/green-light.jpg">
                <img src="img/icons/green.jpg" alt="" height="12" border="0">
                 <xsl:attribute name="width"><xsl:value-of select="round(//votesCount div $totalCount * 200)"  disable-output-escaping="yes"/></xsl:attribute>
                </img>
            </td>
            <td>
            <em>
             (<xsl:value-of select="round(//votesCount div $totalCount * 100)"  disable-output-escaping="yes"/>%)
            </em>
            </td>
            </tr>
            </table>
          </div>
        </td>
      </tr>
      </xsl:if>
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
