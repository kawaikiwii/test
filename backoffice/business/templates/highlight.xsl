<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        highlight.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl">

    <xsl:output method="xml" encoding="utf-8" indent="yes"
        omit-xml-declaration="yes" media-type="text/html" />

<xsl:template match="/">
    <xsl:apply-templates/>
</xsl:template>

<xsl:template match="resultSet">
	<xsl:for-each select="./result">
		<xsl:apply-templates select="."/>
	</xsl:for-each>
</xsl:template>

<xsl:template match="result">
<xsl:variable name="bizobject">
	<xsl:value-of select="name(child::*[1])"/>
</xsl:variable>
<xsl:variable name="objectClass">
	<xsl:value-of select="name(child::*[1])"/>
</xsl:variable>
<xsl:variable name="objectId">
	<xsl:value-of select=".//id[1]"/>
</xsl:variable>
<xsl:variable name="itemSelector">
	<xsl:value-of select="concat($objectClass,'_',$objectId)"/>
</xsl:variable>
<xsl:variable name="dateFormat">
	<xsl:value-of select="php:function('constant', string('_DATE_FORMAT'))"/>	
</xsl:variable>
<xsl:variable name="dateTimeFormat">
	<xsl:value-of select="php:function('constant', string('_DATE_TIME_FORMAT'))"/>	
</xsl:variable>
	<tr align="center">
		<xsl:choose>
			<xsl:when test="$bizobject = 'article'">
				<td class="actions">
				    <div class="toolbar">
				        <ul>
				            <li>
								<xsl:element name="input">
									<xsl:attribute name="type">checkbox</xsl:attribute>
									<xsl:attribute name="id">item_<xsl:value-of select="$itemSelector"/></xsl:attribute>
									<xsl:attribute name="onclick">var command = ($('item_<xsl:value-of select="$itemSelector"/>').checked ? 'addToSessionBin' : 'removeFromSessionBin');manageBin(command, '', '', '<xsl:value-of select="$itemSelector"/>', '', 'compteur', '')</xsl:attribute>
									<xsl:if test="contains(//tempBin, $itemSelector)">
										<xsl:attribute name="checked">
										</xsl:attribute>
									</xsl:if>
								</xsl:element>
				            </li>
							<li>
								<xsl:element name="a">
									<xsl:attribute name="href">#</xsl:attribute>
									<xsl:attribute name="class">add</xsl:attribute>
									<xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></xsl:attribute>
									<xsl:attribute name="onclick">manageBin('addToSelectedBin', '', '', '<xsl:value-of select="$itemSelector"/>', $('selectBin').options[$('selectBin').selectedIndex].value, 'binData', '')</xsl:attribute>
									<span><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></span>
								</xsl:element>
					        </li>
					        <li>
								<xsl:element name="a">
									<xsl:attribute name="href">?_wcmAction=business/<xsl:value-of select="$objectClass"/>&amp;id=<xsl:value-of select="$objectId"/></xsl:attribute>
									<xsl:attribute name="class">edit</xsl:attribute>
									<xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></xsl:attribute>
									<span><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></span>
								</xsl:element>
					        </li>
				        </ul>
				    </div>
				</td>
				<td class="type">
					<xsl:element name="span">
						<xsl:attribute name="class"><xsl:value-of select="$objectClass"/></xsl:attribute>
						<xsl:attribute name="title">
							<xsl:choose>
								<xsl:when test="$objectClass = 'channel'">
									<xsl:value-of select="php:function('constant', string('_BIZ_SECTION'))"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$objectClass"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:value-of select="php:function('ucfirst', $objectClass)"/>
					</xsl:element>
				</td>
				<td class="title" nowrap="nowrap">
					<xsl:element name="u">
						<xsl:attribute name="title">
							<xsl:choose>
								<xsl:when test="$bizobject = 'channel'">
									<xsl:value-of select="php:function('constant', string('_BIZ_SECTION'))"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('ucfirst', $objectClass)"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:attribute name="class">
							info
						</xsl:attribute>
						<xsl:apply-templates select="./*/title[1]"/>
					</xsl:element>
				</td>
				<td class="workflowState">
					<xsl:value-of select="php:function('ucfirst', string(./*/workflowState[1]))"/>
				</td>
				<td class="date">
					<xsl:element name="u">
						<xsl:attribute name="class">info</xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="./*/publicationDate[1]"/></xsl:attribute>
						<xsl:value-of select="./*/publicationDate[1]"/>
					</xsl:element>
				</td>
				<td class="date">
					<xsl:element name="u">
						<xsl:attribute name="class">info</xsl:attribute>
						<xsl:variable name="time">
							<xsl:value-of select="php:function('str_replace','T',' ', string(./*/modifiedAt[1]))"/>
						</xsl:variable>
						<xsl:variable name="modifiedAtTime">
							<xsl:value-of select="php:function('strtotime', $time)"/>
						</xsl:variable>
						<xsl:attribute name="title"><xsl:value-of select="php:function('strftime', $dateFormat, $modifiedAtTime)"/></xsl:attribute>
						<xsl:value-of select="php:function('strftime', $dateFormat, $modifiedAtTime)"/>
					</xsl:element>
				</td>
			</xsl:when>
			<xsl:otherwise>
				<td class="actions">
				    <div class="toolbar">
				        <ul>
				            <li>
								<xsl:element name="input">
									<xsl:attribute name="type">checkbox</xsl:attribute>
									<xsl:attribute name="id">item_<xsl:value-of select="$itemSelector"/></xsl:attribute>
									<xsl:attribute name="onclick">var command = ($('item_<xsl:value-of select="$itemSelector"/>').checked ? 'addToSessionBin' : 'removeFromSessionBin');manageBin(command, '', '', '<xsl:value-of select="$itemSelector"/>', '', 'compteur', '')</xsl:attribute>
									<xsl:if test="contains(//tempBin, $itemSelector)">
										<xsl:attribute name="checked">
										</xsl:attribute>
									</xsl:if>
								</xsl:element>
				            </li>
							<li>
								<xsl:element name="a">
									<xsl:attribute name="href">#</xsl:attribute>
									<xsl:attribute name="class">add</xsl:attribute>
									<xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></xsl:attribute>
									<xsl:attribute name="onclick">manageBin('addToSelectedBin', '', '', '<xsl:value-of select="$itemSelector"/>', $('selectBin').options[$('selectBin').selectedIndex].value, 'binData', '')</xsl:attribute>
					                <span><xsl:value-of select="php:function('constant', string('_BIZ_SEARCH_ADD_TO_SELECTED_BIN'))"/></span>
								</xsl:element>
					        </li>
					        <li>
								<xsl:element name="a">
									<xsl:attribute name="href">?_wcmAction=business/<xsl:value-of select="$objectClass"/>&amp;id=<xsl:value-of select="$objectId"/></xsl:attribute>
									<xsl:attribute name="class">edit</xsl:attribute>
									<xsl:attribute name="title"><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></xsl:attribute>
									<span><xsl:value-of select="php:function('constant', string('_BIZ_EDIT'))"/></span>
								</xsl:element>
					        </li>
				        </ul>
				    </div>
				</td>
				<td class="type">
					<xsl:element name="span">
						<xsl:attribute name="class"><xsl:value-of select="$objectClass"/></xsl:attribute>
						<xsl:attribute name="title">
							<xsl:choose>
								<xsl:when test="$objectClass = 'channel'">
									<xsl:value-of select="php:function('constant', string('_BIZ_SECTION'))"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$objectClass"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
							<xsl:value-of select="php:function('ucfirst', $objectClass)"/>
					</xsl:element>
				</td>
				<td class="title" nowrap="nowrap">
					<xsl:element name="u">
						<xsl:attribute name="title">
							<xsl:choose>
								<xsl:when test="$bizobject = 'channel'">
									<xsl:value-of select="php:function('constant', string('_BIZ_SECTION'))"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="php:function('ucfirst', $objectClass)"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:attribute name="class">
							info
						</xsl:attribute>
						<xsl:apply-templates select="./*/title[1]"/>
					</xsl:element>
				</td>
				<td class="workflowState">
					<xsl:value-of select="php:function('ucfirst', string(./*/workflowState[1]))"/>
				</td>
				<td class="date">
					<xsl:element name="u">
						<xsl:attribute name="class">info</xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="./*/publicationDate[1]"/></xsl:attribute>
						<xsl:value-of select="./*/publicationDate[1]"/>
					</xsl:element>
				</td>
				<td class="date">
					<xsl:element name="u">
						<xsl:attribute name="class">info</xsl:attribute>
						<xsl:variable name="time">
							<xsl:value-of select="php:function('str_replace','T',' ', string(./*/modifiedAt[1]))"/>
						</xsl:variable>
						<xsl:variable name="modifiedAtTime">
							<xsl:value-of select="php:function('strtotime', $time)"/>
						</xsl:variable>
						<xsl:attribute name="title"><xsl:value-of select="php:function('strftime', $dateFormat, $modifiedAtTime)"/></xsl:attribute>
						<xsl:value-of select="php:function('strftime', $dateFormat, $modifiedAtTime)"/>
					</xsl:element>
				</td>
			</xsl:otherwise>
		</xsl:choose>
	</tr>
</xsl:template>

<xsl:template match="hit">
	<strong><xsl:value-of select="." /></strong>
</xsl:template>

</xsl:stylesheet>
<!-- Stylus Studio meta-information - (c) 2004-2006. Progress Software Corporation. All rights reserved.
<metaInformation>
<scenarios ><scenario default="yes" name="Scenario1" userelativepaths="yes" externalpreview="no" url="file:///c:/Documents and Settings/dg/Bureau/highlight.xml" htmlbaseurl="" outputurl="" processortype="internal" useresolver="yes" profilemode="0" profiledepth="" profilelength="" urlprofilexml="" commandline="" additionalpath="" additionalclasspath="" postprocessortype="none" postprocesscommandline="" postprocessadditionalpath="" postprocessgeneratedext="" validateoutput="no" validator="internal" customvalidator="" ><advancedProp name="sInitialMode" value=""/><advancedProp name="bXsltOneIsOkay" value="true"/><advancedProp name="bSchemaAware" value="true"/><advancedProp name="bXml11" value="false"/><advancedProp name="iValidation" value="0"/><advancedProp name="bExtensions" value="true"/><advancedProp name="iWhitespace" value="0"/><advancedProp name="sInitialTemplate" value=""/><advancedProp name="bTinyTree" value="true"/><advancedProp name="bWarnings" value="true"/><advancedProp name="bUseDTD" value="false"/></scenario></scenarios><MapperMetaTag><MapperInfo srcSchemaPathIsRelative="yes" srcSchemaInterpretAsXML="no" destSchemaPath="" destSchemaRoot="" destSchemaPathIsRelative="yes" destSchemaInterpretAsXML="no"/><MapperBlockPosition></MapperBlockPosition><TemplateContext></TemplateContext><MapperFilter side="source"></MapperFilter></MapperMetaTag>
</metaInformation>
-->