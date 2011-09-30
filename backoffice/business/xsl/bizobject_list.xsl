<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        bizobject_list.xsl
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
  <xsl:param name="createdByUserName" select="''"/>
  <xsl:param name="modifiedByUserName" select="''"/>

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

  <!-- Template for rendering article -->
  <xsl:template match="/">
   
  <td class="actions">
    <div class="toolbar">
    <ul>
        <li><input type="checkbox" >
                <xsl:attribute name="id">item_<xsl:value-of select="name(child::node())"/>_<xsl:value-of select="child::node()/id"/></xsl:attribute> 
                <xsl:attribute name="onclick">
                        if (document.getElementById('item_<xsl:value-of select="name(child::node())"/>_<xsl:value-of select="child::node()/id"/>').checked == true)
                            manageBin('addToSessionBin', '','','<xsl:value-of select="name(child::node())"/>_<xsl:value-of select="child::node()/id"/>','','compteur', '');
                        else
                            manageBin('removeFromSessionBin', '','','<xsl:value-of select="name(child::node())"/>_<xsl:value-of select="child::node()/id"/>','','compteur', '');
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
            <xsl:attribute name="onclick">manageBin('addToSelectedBin', '', '', '<xsl:value-of select="name(child::node())"/>_<xsl:value-of select="child::node()/id"/>', document.getElementById('selectBin').options[document.getElementById('selectBin').selectedIndex].value, 'binData', '')</xsl:attribute>
            <span><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></span></a>
        </li>
        <li><a href="#" class="edit">
            <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></xsl:attribute>
            <xsl:if test="$callback != ''">
              <xsl:attribute name="onclick"><xsl:value-of select="$callback"/>('business/<xsl:value-of select="name(child::node())"/>','<xsl:value-of select="child::node()/id"/>', '<xsl:call-template name="formatText"><xsl:with-param name="text" select="child::node()/title"/></xsl:call-template>')</xsl:attribute>
            </xsl:if>
            <span><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></span></a>
        </li>
        <li>
        	<xsl:if test="$locked = 'TRUE'">
                <a href="#" class="lock">
                <xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_LOCK'))"/></xsl:attribute>
                <span><xsl:value-of select="php:function('constant', string('_LOCK'))"/></span></a>
            </xsl:if>
            <xsl:if test="$locked = 'FALSE'">
                <a href="#" class="unlock">
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
  </td>
  <td><span class="article"><span><xsl:value-of select="child::node()/className"/></span></span></td>
  <td class="title"><xsl:value-of select="child::node()/title"/></td>
  <td class="source"><xsl:value-of select="child::node()/source"/></td>
  <td class="date"><xsl:value-of select="child::node()/createdAt"/></td>
  <td><a href="#">
    <xsl:if test="$createdByUserName = '_ADMINISTRATOR'">
        <xsl:value-of select="php:function('constant',string($createdByUserName))"/>
    </xsl:if>
    <xsl:if test="$createdByUserName != '_ADMINISTRATOR'">
        <xsl:value-of select="$createdByUserName"/>
    </xsl:if>
  </a></td>
  <td class="date"><xsl:value-of select="child::node()/modifiedAt"/></td>
  <td><a href="">
    <xsl:if test="$modifiedByUserName = '_ADMINISTRATOR'">
        <xsl:value-of select="php:function('constant',string($modifiedByUserName))"/>
    </xsl:if>
    <xsl:if test="$modifiedByUserName != '_ADMINISTRATOR'">
        <xsl:value-of select="$modifiedByUserName"/>
    </xsl:if>
  </a></td>
   
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