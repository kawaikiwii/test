<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        article_grid.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">
  <xsl:param name="callback" select="''"/>
  <xsl:param name="locked" select="''"/>
  <xsl:param name="checked" select="''"/>

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>

  <!-- Template for rendering article -->
  <xsl:template match="article">
    <div class="box">
        <div class="toolbar">
            <ul>
                <li><input type="checkbox" >
                    <xsl:attribute name="id">item_<xsl:value-of select="className"/>_<xsl:value-of select="id"/></xsl:attribute>
                    <xsl:attribute name="onclick">
                            if (document.getElementById('item_<xsl:value-of select="className"/>_<xsl:value-of select="id"/>').checked == true)
                                manageBin('addToSessionBin', '','','<xsl:value-of select="className"/>_<xsl:value-of select="id"/>','','compteur', '');
                            else
                                manageBin('removeFromSessionBin', '','','<xsl:value-of select="className"/>_<xsl:value-of select="id"/>','','compteur', '');
                    </xsl:attribute>
                    <xsl:if test="$checked != ''">
                        <xsl:attribute name="checked">
                            checked
                        </xsl:attribute>
                        </xsl:if>
                    </input>
                </li>
                <li><a href="#" class="add">
                    <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></xsl:attribute>
                    <xsl:attribute name="onclick">manageBin('addToSelectedBin', '', '', '<xsl:value-of select="className"/>_<xsl:value-of select="id"/>', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value, 'binData', '')</xsl:attribute>
                    <span><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></span></a>
                </li>
                <li><a href="" class="edit">
                    <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></xsl:attribute>
                    <xsl:if test="$callback != ''">
                        <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('business/article',<xsl:value-of select="id"/>, '<xsl:call-template name="formatText"><xsl:with-param name="text" select="title"/></xsl:call-template>')</xsl:attribute>
                    </xsl:if>
                    <span><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></span></a>
                </li>
                <li>
                    <xsl:if test="$locked = 'TRUE'">
                        <a href="#" class="unlock">
                        <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_LOCK'))"/></xsl:attribute>
                        <span><xsl:value-of select="php:function('constant', string('_LOCK'))"/></span></a>
                    </xsl:if>
                    <xsl:if test="$locked = 'FALSE'">
                        <a href="#" class="lock">
                        <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_LOCK'))"/></xsl:attribute>
                        <span><xsl:value-of select="php:function('constant', string('_LOCK'))"/></span></a>
                    </xsl:if>
                    <xsl:if test="$locked = 'ME'">
                        <a href="#" class="lock">
                        <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_LOCK'))"/></xsl:attribute>
                        <span><xsl:value-of select="php:function('constant', string('_LOCK'))"/></span></a>
                    </xsl:if>
                </li>
            </ul>
        </div>
        <div class="preview photo">
            <a href=""><img src="preview/flowers-100.jpg" width="65" height="100" alt="" /></a>
        </div>
        <div class="metadata">
            <table>
                <tr>
                    <th><xsl:value-of select="php:function('constant', string('_BIZ_SOURCE'))"/></th>
                    <td><xsl:value-of select="source"/></td>
                </tr>
                <tr>
                    <th><xsl:value-of select="php:function('constant', string('_BIZ_CREATED_AT'))"/></th>
                    <td><xsl:value-of select="createdAt"/></td>
                </tr>
                <tr>
                    <th><xsl:value-of select="php:function('constant', string('_BIZ_MODIFIED_AT'))"/></th>
                    <td><xsl:value-of select="modifiedAt"/></td>

                </tr>
            </table>
        </div>
    </div>
  </xsl:template>
  <xsl:template name="formatText">
    <xsl:param name="text"/>
    <xsl:variable name="apos">
        <xsl:text>'</xsl:text>
    </xsl:variable>
    <xsl:value-of select="php:function('str_replace', $apos, '&amp;apos;', string($text))" />
  </xsl:template>
  <!-- /Template for rendering article -->

</xsl:stylesheet>