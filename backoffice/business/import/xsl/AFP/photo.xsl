<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        photo.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="xml" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>

  <xsl:template match="/">
    <photo>
        <title>
            <xsl:value-of select="NewsComponent/NewsLines/HeadLine" disable-output-escaping="yes" />
        </title>
        <caption>
            <xsl:value-of select="NewsComponent/NewsComponent[Role/@FormalName='Caption']/ContentItem/DataContent" disable-output-escaping="yes" />
        </caption>
        <credits>
            <xsl:value-of select="NewsComponent/AdministrativeMetadata/Creator/Party/@FormalName" disable-output-escaping="yes" />
            <xsl:text disable-output-escaping="yes"> </xsl:text> 
            <xsl:value-of select="NewsComponent/AdministrativeMetadata/Provider/Party/@FormalName" disable-output-escaping="yes" />
        </credits>
        <height>
            <xsl:value-of select="NewsComponent/NewsComponent[Role/@FormalName='Preview']/ContentItem/Characteristics/Property[@FormalName='Height']/@Value" disable-output-escaping="yes" />
        </height>
        <width>
            <xsl:value-of select="NewsComponent/NewsComponent[Role/@FormalName='Preview']/ContentItem/Characteristics/Property[@FormalName='Width']/@Value" disable-output-escaping="yes" />
        </width>
        <thumbHeight>
            <xsl:value-of select="NewsComponent/NewsComponent[Role/@FormalName='Thumbnail']/ContentItem/Characteristics/Property[@FormalName='Height']/@Value" disable-output-escaping="yes" />
        </thumbHeight>
        <thumbWidth>
            <xsl:value-of select="NewsComponent/NewsComponent[Role/@FormalName='Thumbnail']/ContentItem/Characteristics/Property[@FormalName='Width']/@Value" disable-output-escaping="yes" />
        </thumbWidth>
    </photo>
  </xsl:template>

</xsl:stylesheet>