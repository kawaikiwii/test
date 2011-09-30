<?xml version="1.0" encoding="utf-8"?>
<!--
 * Project:     WCM
 * File:        wcm.nSemanticSort.xsl
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" encoding="utf-8" indent="yes" omit-xml-declaration="yes" media-type="text/html"/>
    
  <!-- ================================================================== -->
  <!-- -->
  <!-- ================================================================== -->
  <xsl:template match="NServer">
    <xsl:apply-templates select="Concepts"/>
    <xsl:apply-templates select="Categories"/>
    <xsl:apply-templates select="EntitiesList"/>
    <xsl:apply-templates select="Sentiment"/>
    <xsl:apply-templates select="Summary"/>
    <xsl:apply-templates select="SimilarTexts"/>
  </xsl:template>

  <!-- Concepts -->
  <xsl:template match="Concepts">
    <Concepts>
      <xsl:for-each select="Concept">
        <xsl:sort select="@RelevancyScore" data-type="number" order="descending"/>
        <Concept>
          <xsl:attribute name="Source">
            <xsl:value-of select="@Source"/>
          </xsl:attribute>
          <xsl:attribute name="Kind">
            <xsl:value-of select="@Kind"/>
          </xsl:attribute>
          <xsl:attribute name="Frequency">
            <xsl:value-of select="@Frequency"/>
          </xsl:attribute>
          <xsl:attribute name="RelevancyScore">
            <xsl:value-of select="@RelevancyScore"/>
          </xsl:attribute>
          <xsl:value-of select="."/>
        </Concept>
      </xsl:for-each>
    </Concepts>
  </xsl:template>

  <!-- Categories -->
  <xsl:template match="Categories">
    <!-- todo -->
  </xsl:template>

  <!-- Sentiment -->
  <xsl:template match="Sentiment">
    <xsl:copy-of select="."/>
  </xsl:template>

  <!-- Summary -->
  <xsl:template match="Summary">
    <xsl:copy-of select="."/>
  </xsl:template>

  <xsl:template match="EntitiesList">
    <EntitiesList>
      <Entities Kind="ON">
        <xsl:for-each select="Entities[@Kind='ON']/Entity">
          <xsl:sort select="@Weight" data-type="number" order="descending"/>
          <Entity>
            <xsl:attribute name="Source">
              <xsl:value-of select="@Source"/>
            </xsl:attribute>
            <xsl:attribute name="Kind">
              <xsl:value-of select="@Kind"/>
            </xsl:attribute>
            <xsl:attribute name="Weight">
              <xsl:value-of select="@Weight"/>
            </xsl:attribute>
            <xsl:attribute name="RelevancyScore">
              <xsl:value-of select="@RelevancyScore"/>
            </xsl:attribute>
            <xsl:attribute name="ConfidenceScore">
              <xsl:value-of select="@ConfidenceScore"/>
            </xsl:attribute>
            <xsl:attribute name="Frequency">
              <xsl:value-of select="@Frequency"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
          </Entity>
        </xsl:for-each>
      </Entities>
      <Entities Kind="PN">
        <xsl:for-each select="Entities[@Kind='PN']/Entity">
          <xsl:sort select="@Weight" data-type="number" order="descending"/>
          <Entity>
            <xsl:attribute name="Source">
              <xsl:value-of select="@Source"/>
            </xsl:attribute>
            <xsl:attribute name="Kind">
              <xsl:value-of select="@Kind"/>
            </xsl:attribute>
            <xsl:attribute name="Weight">
              <xsl:value-of select="@Weight"/>
            </xsl:attribute>
            <xsl:attribute name="RelevancyScore">
              <xsl:value-of select="@RelevancyScore"/>
            </xsl:attribute>
            <xsl:attribute name="ConfidenceScore">
              <xsl:value-of select="@ConfidenceScore"/>
            </xsl:attribute>
            <xsl:attribute name="Frequency">
              <xsl:value-of select="@Frequency"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
          </Entity>
        </xsl:for-each>
      </Entities>
      <Entities Kind="GL">
        <xsl:for-each select="Entities[@Kind='GL']/Entity">
          <xsl:sort select="@Weight" data-type="number" order="descending"/>
          <Entity>
            <xsl:attribute name="Source">
              <xsl:value-of select="@Source"/>
            </xsl:attribute>
            <xsl:attribute name="Kind">
              <xsl:value-of select="@Kind"/>
            </xsl:attribute>
            <xsl:attribute name="Weight">
              <xsl:value-of select="@Weight"/>
            </xsl:attribute>
            <xsl:attribute name="RelevancyScore">
              <xsl:value-of select="@RelevancyScore"/>
            </xsl:attribute>
            <xsl:attribute name="ConfidenceScore">
              <xsl:value-of select="@ConfidenceScore"/>
            </xsl:attribute>
            <xsl:attribute name="Frequency">
              <xsl:value-of select="@Frequency"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
          </Entity>
        </xsl:for-each>
      </Entities>
    </EntitiesList>
  </xsl:template>

  <xsl:template match="Entity">
    <Entity>
      <xsl:attribute name="Source">
        <xsl:value-of select="@Source"/>
      </xsl:attribute>
      <xsl:attribute name="Kind">
        <xsl:value-of select="@Kind"/>
      </xsl:attribute>
      <xsl:attribute name="Weight">
        <xsl:value-of select="@Weight"/>
      </xsl:attribute>
      <xsl:attribute name="RelevancyScore">
        <xsl:value-of select="@RelevancyScore"/>
      </xsl:attribute>
      <xsl:attribute name="ConfidenceScore">
        <xsl:value-of select="@ConfidenceScore"/>
      </xsl:attribute>
      <xsl:attribute name="Frequency">
        <xsl:value-of select="@Frequency"/>
      </xsl:attribute>
      <xsl:value-of select="."/>
    </Entity>
  </xsl:template>

  <!-- SimilarTexts -->
  <xsl:template match="SimilarTexts">
    <SimilarTexts>
      <xsl:for-each select="SimilarText">
        <xsl:sort select="@Weight" data-type="number" order="descending"/>
        <SimilarText>
          <xsl:attribute name="Id">
            <xsl:value-of select="@Id"/>
          </xsl:attribute>
          <xsl:attribute name="Source">
            <xsl:value-of select="@Source"/>
          </xsl:attribute>
          <xsl:attribute name="Weight">
            <xsl:value-of select="@Weight"/>
          </xsl:attribute>
          <xsl:value-of select="."/>
        </SimilarText>
      </xsl:for-each>
    </SimilarTexts>
  </xsl:template>
</xsl:stylesheet>
