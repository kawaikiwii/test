<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        quickSearch.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">

    <xsl:param name="baseURL" select="''"/>
    <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

    <xsl:template match="/">
        <xsl:apply-templates select="resultSet"/>
    </xsl:template>

    <xsl:template match="resultSet">
        <ul>
            <xsl:apply-templates select="result"/>
        </ul>
    </xsl:template>

    <xsl:template match="result">
        <li class="bizobject">
            <xsl:variable name="objectClass" select="name(child::*[position()=1])"/>
            <xsl:variable name="objectId" select="child::*[position()=1]/id"/>
            <xsl:attribute name="id"><xsl:value-of select="$objectClass"/>-<xsl:value-of select="$objectId" /></xsl:attribute>
            <div class="toolbar">
                <div class="button">
                    <a href="" class="add"><span>Add</span></a>
                </div>
                <xsl:value-of select="$objectClass"/>
            </div>
            <xsl:apply-templates />
        </li>
    </xsl:template>

    <xsl:template match="article">
        <div class="content">
            <b><xsl:value-of select="title" /></b><br/>
            <xsl:value-of select="abstract" disable-output-escaping="yes"/>
        </div>
    </xsl:template>

    <xsl:template match="photo">
        <div class="content" style="height:100px; overflow:hidden">
            <img height="80" src="./{thumbnail}" alt="" hspace="2" vspace="2" style="float:left;"/>
            <b><xsl:value-of select="title" /></b><br/>
            <xsl:value-of select="caption" disable-output-escaping="yes"/>
        </div>
    </xsl:template>

    <xsl:template match="contribution">
        <div class="content">
            <b><xsl:value-of select="nickname" /></b><br/>
            <xsl:value-of select="text" disable-output-escaping="yes"/>
        </div>
    </xsl:template>

    <xsl:template match="site|channel|forum|poll|slideshow">
        <div class="content">
            <b><xsl:value-of select="title" /></b><br/>
            <xsl:value-of select="description" disable-output-escaping="yes"/>
        </div>
    </xsl:template>
</xsl:stylesheet>
