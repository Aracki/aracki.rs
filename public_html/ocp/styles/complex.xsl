<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="complex">
	<xsl:param name="style"/>
	<xsl:param name="name"/>
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>
	<select class="ocp_forma" multiple="" size="10">
		<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="$name"/>[]</xsl:attribute>
		<option value="" > </option>
		<xsl:call-template name="divideForeign">
			<xsl:with-param name="to-be-divided" select="$to-be-divided"/>
			<xsl:with-param name="labels" select="$labels"/>
			<xsl:with-param name="selected" select="concat(', ',$selected,',')"/>
			<xsl:with-param name="delimiter" select="$delimiter"/>
		</xsl:call-template>
	</select>
 </xsl:template>

 <xsl:template name="divideForeign">
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>
	<xsl:choose>
	<xsl:when test="contains($to-be-divided,$delimiter)">
		<option>
		<xsl:attribute name="value"><xsl:value-of select='substring-before($to-be-divided,$delimiter)'/></xsl:attribute>
		<xsl:if test="contains($selected, concat(', ', substring-before($to-be-divided,$delimiter), ','))">
			<xsl:attribute name="selected"></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="substring-before($labels,$delimiter)"/>
		</option>
		<xsl:call-template name="divideForeign">
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="labels" select="substring-after($labels,$delimiter)"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="$delimiter"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<option>
		<xsl:attribute name="value"><xsl:value-of select='$to-be-divided'/></xsl:attribute>
		<xsl:if test="contains($selected, concat(', ', $to-be-divided, ','))">
			<xsl:attribute name="selected"></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="$labels"/>
		</option>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

 </xsl:stylesheet> 