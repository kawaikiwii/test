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
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="site|channel|article|slideshow|contribution|poll|forum|issue|publication">
        <li>
            <a>
                <xsl:attribute name="href">
                    <xsl:value-of select="$baseURL"/>?_wcmAction=business/<xsl:value-of select="name()"/>&amp;_wcmTodo=view&amp;id=<xsl:value-of select="id"/>
                </xsl:attribute>
                <xsl:value-of select="title"/>
            </a>
        </li>       
    </xsl:template>

    <xsl:template match="photo">
        <li>
            <a>
                <xsl:attribute name="href">
                    <xsl:value-of select="$baseURL"/>?_wcmAction=business/<xsl:value-of select="name()"/>&amp;_wcmTodo=view&amp;id=<xsl:value-of select="id"/>
                </xsl:attribute>
                <xsl:value-of select="title"/>
                (<xsl:value-of select="width"/>x<xsl:value-of select="height"/>)
            </a>
        </li>       
    </xsl:template>
</xsl:stylesheet>