<?xml version="1.0" encoding="UTF-8"?>
<!--
 * Project:     WCM
 * File:        wcm_tree.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">
    <xsl:param name="mode" select="'tree'"/>
    <xsl:param name="baseUrl" select="''"/>
    <xsl:param name="jsCallback" select="'_wcmOnSelect'"/>
    <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>

    <xsl:template match="/">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="tree">
        <script language="javascript" type="text/javascript">

            function _wcmTree(tree, path, command) {
                wcmSysAjaxController.call("wcm.ajaxTreeview", {
                    tree: tree,
                    path: path,
                    command: command
                });
            }
            _wcmOnSelect = function(folder, parent)
            {
                $('id').value = folder;
                $('name').value = folder;
                $('categoryId').value = parent;
                closemodal();
            }
        </script>

        <div>
            <xsl:for-each select="node">
                <div>
                    <xsl:attribute name="id"><xsl:value-of select="@path"/></xsl:attribute>
                    <xsl:apply-templates select="."/>
                </div>
            </xsl:for-each>
        </div>
    </xsl:template>
    <xsl:template match="node">
        <xsl:variable name="path">
            <xsl:value-of select="@path"/>
        </xsl:variable>
        <xsl:variable name="nodeName">
            <xsl:value-of select="caption"/>
        </xsl:variable>
        <xsl:variable name="parent">
            <xsl:value-of select="php:function('parsePath', $path, $nodeName)"/>
        </xsl:variable>
        <table cellspacing="0" cellpadding="1" border="0">
        <xsl:if test="php:function('ereg', '.tpl', $nodeName) = 0">
        <tr>
            <xsl:if test="@depth != '0' and @depth != ''">
            <td>
                <img alt="" height="16" border="0">
                    <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/pixel.gif</xsl:attribute>
                    <xsl:attribute name="width"><xsl:value-of select="18 * (0 + @depth)"/></xsl:attribute>
                </img>
            </td>
            </xsl:if>
            <td>
                <xsl:choose>
                <xsl:when test="@expanded = '1' and count(node)=0">
                    <img alt="" width="16" height="16" border="0">
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/pixel.gif</xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:when test="@expanded = '1'">
                    <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                        <xsl:attribute name="onclick">_wcmTree('<xsl:value-of select="tree"/>', '<xsl:value-of select="@path"/>', 'collapse')</xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/collapse.gif</xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:otherwise>
                    <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                        <xsl:attribute name="onclick">_wcmTree('<xsl:value-of select="tree"/>', '<xsl:value-of select="@path"/>', 'expand')</xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/expand.gif</xsl:attribute>
                    </img>
                </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                    <xsl:attribute name="onclick"><xsl:value-of select="$jsCallback"/>('<xsl:value-of select="caption"/>', '<xsl:value-of select="$parent"/>')</xsl:attribute>
                    <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/icons/<xsl:value-of select="icon"/></xsl:attribute>
                </img>
            </td>
            <td>
                <nobr>
                    <a>
                        <xsl:attribute name="href">javascript:<xsl:value-of select="$jsCallback"/>('<xsl:value-of select="caption"/>', '<xsl:value-of select="$parent"/>')</xsl:attribute>
                        <xsl:if test="@selected = '1'">
                            <xsl:attribute name="class">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="caption"/>
                    </a>
                </nobr>
            </td>
        </tr>
        </xsl:if>
        <xsl:if test="php:function('ereg', '.tpl', $nodeName) != 0">
        <tr>
            <xsl:if test="@depth != '0' and @depth != ''">
            <td>
                <img alt="" height="16" border="0">
                    <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/pixel.gif</xsl:attribute>
                    <xsl:attribute name="width"><xsl:value-of select="18 * (0 + @depth)"/></xsl:attribute>
                </img>
            </td>
            </xsl:if>
            <td>
                <xsl:choose>
                <xsl:when test="@expanded = '1' and count(node)=0">
                    <img alt="" width="16" height="16" border="0">
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/pixel.gif</xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:when test="@expanded = '1'">
                    <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                        <xsl:attribute name="onclick">_wcmTree('<xsl:value-of select="tree"/>', '<xsl:value-of select="@path"/>', 'collapse')</xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/collapse.gif</xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:otherwise>
                    <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                        <xsl:attribute name="onclick">_wcmTree('<xsl:value-of select="tree"/>', '<xsl:value-of select="@path"/>', 'expand')</xsl:attribute>
                        <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/expand.gif</xsl:attribute>
                    </img>
                </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <img alt="" width="16" height="16" border="0" style="cursor:pointer">
                    <xsl:attribute name="onclick"><xsl:value-of select="$jsCallback"/>('<xsl:value-of select="caption"/>', '<xsl:value-of select="$parent"/>')</xsl:attribute>
                    <xsl:attribute name="src"><xsl:value-of select="$baseUrl"/>img/icons/<xsl:value-of select="icon"/></xsl:attribute>
                </img>
            </td>
            <td>
                <nobr>
                    <a>
                        <xsl:attribute name="href">javascript:<xsl:value-of select="$jsCallback"/>('<xsl:value-of select="caption"/>', '<xsl:value-of select="$parent"/>')</xsl:attribute>
                        <xsl:if test="@selected = '1'">
                            <xsl:attribute name="class">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:value-of select="caption"/>
                    </a>
                </nobr>
            </td>
        </tr>
        </xsl:if>
        </table>
        <xsl:if test="@expanded = '1'">
            <xsl:for-each select="node">
                <div>
                    <xsl:attribute name="id"><xsl:value-of select="@path"/></xsl:attribute>
                    <xsl:apply-templates select="."/>
                </div>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>