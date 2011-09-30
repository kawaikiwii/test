<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        wcm.nSemanticDisplay.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">
    <xsl:param name="pk" />

    <xsl:output method="xml" encoding="utf-8" indent="yes"
        omit-xml-declaration="yes" media-type="text/html" />

    <xsl:template match="/">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="article|photo|channel|slideshow|contribution|poll|newsitem|newsletter|webuser|collection|forum|item|publication|issue">
            <div class="toolbar">
                <div class="remove"><span>Remove</span></div>
                <xsl:value-of select="name()" />
            </div>
            <div class="relproperties">
                <xsl:value-of select="php:function('constant','_TITLE')" /> : 
                <input type="hidden" name="_list{$pk}[destinationClass][]" value="{name()}" />
                <input type="hidden" id="{id}" name="_list{$pk}[destinationId][]" value="{id}" />
                <input type="text" name="_list{$pk}[title][]" value="{title}" />
                <img src="./{thumbnail}" alt="" style="margin-left: 46px; margin-top: 4px"/>
            </div>
    </xsl:template>

    <xsl:template match="wcmBizrelation">
        <li class="bizrelation" id="rel-{destinationClass}-{destinationId}">
            <div class="toolbar">
                <div class="remove"><span>Remove</span></div>
                <xsl:value-of select="destinationClass" />
            </div>
            <div class="relproperties">
                <xsl:value-of select="php:function('constant','_TITLE')" /> : 
                <input type="hidden" name="_list{$pk}[destinationClass][]" value="{destinationClass}" />
                <input type="hidden" id="{id}" name="_list{$pk}[destinationId][]" value="{destinationId}" />
                <input type="text" name="_list{$pk}[title][]" value="{title}" />
            </div>
        </li>
    </xsl:template>
        
</xsl:stylesheet>
