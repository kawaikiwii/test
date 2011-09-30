<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        article.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>

  <xsl:template match="/">
    <article>
        <title>
            <xsl:value-of select="nitf/body/body.head/hedline/hl1" disable-output-escaping="yes" />
        </title>
        <subtitle>
            <xsl:value-of select="nitf/body/body.head/hedline/hl2" disable-output-escaping="yes" />
        </subtitle>
        <keywords>
            <xsl:value-of select="nitf/head/title" disable-output-escaping="yes" />
        </keywords>
        <author>
            <xsl:value-of select="nitf/body/body.head/byline" disable-output-escaping="yes" />
            <xsl:text disable-output-escaping="yes"> </xsl:text> 
            <xsl:value-of select="nitf/head/docdata/doc.copyright/@holder" disable-output-escaping="yes" />
        </author>
        <text>
            <xsl:apply-templates select="nitf/body/body.content" />
        </text>
    </article>
  </xsl:template>

    <xsl:template match="body.content">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="table">
        <p>
            <table cellspacing="1" cellpadding="2" border="0" bgcolor="#c0c0c0">
                <xsl:if test="@width   != ''"> <xsl:attribute name="width">   <xsl:value-of select="@width"/>   </xsl:attribute> </xsl:if>
                <xsl:if test="@height  != ''"> <xsl:attribute name="height">  <xsl:value-of select="@height"/>  </xsl:attribute> </xsl:if>
                <xsl:apply-templates select="tbody|tr"/>
            </table>
        </p>
    </xsl:template>

    <xsl:template match="tbody">
        <xsl:apply-templates select="tr"/>
    </xsl:template>

    <xsl:template match="tr">
        <tr bgcolor="#ffffff">
            <xsl:if test="@valign  != ''"> <xsl:attribute name="valign"> <xsl:value-of select="@valign"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@vAlign  != ''"> <xsl:attribute name="valing"> <xsl:value-of select="@vAlign"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@height  != ''"> <xsl:attribute name="height"> <xsl:value-of select="@height"/> </xsl:attribute> </xsl:if>
            <xsl:apply-templates select="td"/>
        </tr>
    </xsl:template>

    <xsl:template match="td">
        <td>
            <xsl:if test="@rowSpan != ''"> <xsl:attribute name="rowspan"> <xsl:value-of select="@rowSpan"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@rowspan != ''"> <xsl:attribute name="rowspan"> <xsl:value-of select="@rowspan"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@colSpan != ''"> <xsl:attribute name="colspan"> <xsl:value-of select="@colSpan"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@colspan != ''"> <xsl:attribute name="colspan"> <xsl:value-of select="@colspan"/> </xsl:attribute> </xsl:if>
            <xsl:if test="@align   != ''"> <xsl:attribute name="align">   <xsl:value-of select="@align"/>   </xsl:attribute> </xsl:if>
            <xsl:if test="@valign  != ''"> <xsl:attribute name="valign">  <xsl:value-of select="@valign"/>  </xsl:attribute> </xsl:if>
            <xsl:if test="@vAlign  != ''"> <xsl:attribute name="valign">  <xsl:value-of select="@vAlign"/>  </xsl:attribute> </xsl:if>
            <xsl:if test="@width   != ''"> <xsl:attribute name="width">   <xsl:value-of select="@width"/>   </xsl:attribute> </xsl:if>
            <xsl:if test="@height  != ''"> <xsl:attribute name="height">  <xsl:value-of select="@height"/>  </xsl:attribute> </xsl:if>
            <xsl:apply-templates />
        </td>
    </xsl:template>

    <xsl:template match="a">
        <xsl:choose>
            <xsl:when test="@class = 'event'">
                <xsl:value-of select="."/>
            </xsl:when>
            <xsl:when test="@class = 'document'">
                <xsl:value-of select="."/>
            </xsl:when>
            <xsl:otherwise>
                <a target='_blank'>
                    <xsl:attribute name="href"> <xsl:value-of select="@href"/> </xsl:attribute>
                    <xsl:if test="@class  != ''"> <xsl:attribute name="class">  <xsl:value-of select="@class"/>  </xsl:attribute> </xsl:if>
                    <xsl:apply-templates />
                </a>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="div">
        <div> <xsl:apply-templates/> </div>
    </xsl:template>

    <xsl:template match="span">
        <span> <xsl:apply-templates/> </span>
    </xsl:template>

    <xsl:template match="p">
        <p> <xsl:apply-templates/> </p>
    </xsl:template>

    <xsl:template match="ul">
        <ul> <xsl:apply-templates/> </ul>
    </xsl:template>

    <xsl:template match="li">
        <li> <xsl:apply-templates/> </li>
    </xsl:template>

    <xsl:template match="ol">
        <ol> <xsl:apply-templates/> </ol>
    </xsl:template>

    <xsl:template match="b">
        <b> <xsl:apply-templates/> </b>
    </xsl:template>

    <xsl:template match="i">
        <i> <xsl:apply-templates/> </i>
    </xsl:template>

    <xsl:template match="quote">
        <quote> <xsl:apply-templates/> </quote>
    </xsl:template>

    <xsl:template match="cite">
        <cite> <xsl:apply-templates/> </cite>
    </xsl:template>

    <xsl:template match="strong">
        <strong> <xsl:apply-templates/> </strong>
    </xsl:template>

    <xsl:template match="code">
        <code> <xsl:apply-templates/> </code>
    </xsl:template>

    <xsl:template match="br">
        <br/>
    </xsl:template>

    <xsl:template match="*">
    </xsl:template>

</xsl:stylesheet>