<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="divideChoose">
	<xsl:param name="to-be-divided"/>
	<xsl:param name="delimiter"/>
	<xsl:param name="start"/>
	<xsl:param name="counter"/>
	
	<xsl:choose>
	<xsl:when test="contains($to-be-divided,$delimiter)">
		<option>
			<xsl:attribute name="value"><xsl:value-of select='$counter'/></xsl:attribute>
			<xsl:if test="$counter = $start">
				<xsl:attribute name="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select='substring-before($to-be-divided,$delimiter)'/>
		</option>
		<xsl:call-template name="divideChoose">
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
			<xsl:with-param name="start" select="$start"/>
			<xsl:with-param name="counter" select="$counter+1"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<option>
			<xsl:attribute name="value"><xsl:value-of select='$counter'/></xsl:attribute>
			<xsl:if test="$counter = $start">
				<xsl:attribute name="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select='$to-be-divided'/>
		</option>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

 </xsl:stylesheet> 