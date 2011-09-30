<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        wcm_search.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="mode" select="'list'"/>
    <xsl:param name="callback" select="''"/>
    <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>
    
    <xsl:template match="/">
        <xsl:apply-templates select="sysobject" />
    </xsl:template>

    <xsl:template match="sysobject">
        <table cellspacing="0" cellpadding="4" border="0">
        <tr>
            <td bgcolor="#f0f0f0">
                <img alt="" width="16" height="16" border="0">
                    <xsl:attribute name="src"> img/icons/<xsl:value-of select="translate(substring(@class,4),'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>.gif </xsl:attribute>
                </img>
            </td>
            <td>
                <div style="cursor:pointer">
                    <xsl:if test="$callback != ''">
                        <xsl:attribute name="onClick"><xsl:value-of select="$callback"/>('<xsl:value-of select="@class"/>',<xsl:value-of select="id"/>)</xsl:attribute>
                    </xsl:if>
                    <b> <xsl:value-of select="name"/> </b> <br/>
                    Id : <xsl:value-of select="id"/> -
                    Creation : <xsl:value-of select="createdAt"/> - 
                    Modification : <xsl:value-of select="createdAt"/> <br/>
                </div>
            </td>
        </tr>
        </table>
    </xsl:template>

</xsl:stylesheet>