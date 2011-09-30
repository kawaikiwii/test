<xsl:stylesheet 
    version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xmlns:ipub="http://www.ipublisher.nl/4.0"
    xmlns:exsl="http://exslt.org/common"
    xmlns:str="http://exslt.org/strings"
    xmlns:date="http://exslt.org/dates-and-times"
    extension-element-prefixes="str exsl date"
    >
<xsl:include href="str.replace.function.xsl"/>  
<xsl:output method="html" encoding="utf-8" indent="yes" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" media-type="text/html"/>

<xsl:template match="/model">
    <html>
    <head>
        <title>WCM Web Services</title>
        <link rel="stylesheet" href="css/doc.css" type="text/css" ></link>
    </head>
    <body>
    <div id="header"> &#160; </div>
    <a name="top"/>
    <div id="directory">
        <h1>Webservices directory</h1>
        <div style="margin-left:10px">
            <ul>
            <xsl:for-each select="/model/menu/*">
                <li>
                <xsl:variable name="className"><xsl:value-of select="/model/class/name"/></xsl:variable>
                <xsl:if test="name = $className">
                    <xsl:value-of select="name"/> <br/>
                </xsl:if>
                <xsl:if test="name != $className">
                    <a href="?class={name}"><xsl:value-of select="name"/></a> <br/>
                </xsl:if>
                </li>
            </xsl:for-each>
            </ul>
        </div>
    </div>
    <div id="main">
        <div id="mainheader">
            <xsl:if test="class != ''">
                <h1>
                    <xsl:value-of select="class/name" />
                    <xsl:variable name="classes"><xsl:for-each select="/model/menu/*">|<xsl:value-of select="."/>|</xsl:for-each></xsl:variable>
                    <xsl:if test="contains($classes, class/name)">
                        &#160;[<a href="?class={class/name}&amp;wsdl">WSDL</a>]
                    </xsl:if>
                </h1>
            </xsl:if>
        </div>
        <div id="mainpadded">
        <table cellpadding="0" cellspacing="0">
        <tr>
        <td id="content">
                <xsl:if test="fault != ''">
                    <xsl:value-of select="fault" />
                </xsl:if>
            <xsl:if test="class != '' and not(fault)">

                <h2>Description</h2>
                <p>
                    <xsl:variable name="lines" select="str:tokenize(class/fullDescription, '|')"/>
                    <xsl:for-each select="$lines">
                        <xsl:value-of select="."/><br/>
                    </xsl:for-each>
                </p>

                <h2>Properties</h2>
                <xsl:if test="count(class/properties/*) = 0">None</xsl:if>
                <xsl:for-each select="class/properties/*">
                    <a name="property_{name}"></a>
                    <div class="property{warning}">
                    <xsl:choose>
                        <xsl:when test="type != ''">
                            <xsl:choose>
                                <xsl:when test="contains('int,boolean,double,float,string,void,int[],boolean[],double[],float[],string[]', type)">
                                    <xsl:value-of select="type" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <a href="?class={str:replace(type,'[]','')}"><xsl:value-of select="type" /></a>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class='warning'><img src='images/doc/warning.gif'/> missing type info</div><br />
                        </xsl:otherwise>
                    </xsl:choose>
                    &#160;<b><xsl:value-of select="name" /></b><br />
                    <xsl:variable name="lines" select="str:tokenize(fullDescription, '|')"/>
                    <xsl:for-each select="$lines">
                        <xsl:value-of select="."/><br/>
                    </xsl:for-each>
                    </div>
                </xsl:for-each>

                <h2>Methods</h2>
                <xsl:if test="count(class/methods/*) = 0">None</xsl:if>
                <xsl:for-each select="class/methods/*">
                    <a name="method_{name}"></a>
                    <div class="method{warning}">
                    <b><xsl:value-of select="name" /></b>(<br/>
                    <table>
                        <xsl:for-each select="params/*">
                            <tr>
                                <td>
                                    <xsl:choose>
                                        <xsl:when test="contains('int,boolean,double,float,string,void,int[],boolean[],double[],float[],string[]', type)">
                                            <xsl:value-of select="type"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <a href="?class={str:replace(type,'[]','')}"><xsl:value-of select="type"/></a>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </td>
                                <td><b><xsl:value-of select="name"/></b><xsl:if test="position() != last()">,</xsl:if></td>
                                <td><nobr><i><xsl:value-of select="comment"/></i></nobr></td>
                            </tr>
                        </xsl:for-each>
                    </table>
                    )<br />
                    <xsl:choose>
                        <xsl:when test="return != ''">
                            <table>
                                <tr>
                                    <td>
                                        <xsl:choose>
                                            <xsl:when test="contains('int,boolean,double,float,string,void,int[],boolean[],double[],float[],string[]', return)">
                                                returns <xsl:value-of select="return"/>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                returns <a href="?class={str:replace(return,'[]','')}"><xsl:value-of select="return"/></a>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </td>
                                    <td><i><xsl:value-of select="returnComment"/></i></td>
                                </tr>
                            </table>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class='warning'><img src='images/doc/warning.gif'/> missing return value</div><br />
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="throws != ''">
                            <i>throws  <xsl:value-of select="throws" /></i><br />
                        </xsl:when>
                    </xsl:choose>
                    <xsl:variable name="lines" select="str:tokenize(fullDescription, '|')"/>
                    <xsl:for-each select="$lines">
                        <xsl:value-of select="."/><br/>
                    </xsl:for-each>
                    </div>
                </xsl:for-each>
            </xsl:if>
        </td>
        </tr>
        </table>
        </div>
    </div>
    </body>
    </html>
</xsl:template>
</xsl:stylesheet>