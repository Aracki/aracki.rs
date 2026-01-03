<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="datum">
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<xsl:param name="labCalendar"/>
	<xsl:param name="hasTime"/>

	<xsl:variable name="yyyy"><xsl:if test="$value != ''"><xsl:value-of select="substring($value, 1, 4)"/></xsl:if></xsl:variable>
	<xsl:variable name="mm"><xsl:if test="$value != ''"><xsl:value-of select="substring($value, 6, 2)"/></xsl:if></xsl:variable>
	<xsl:variable name="dd"><xsl:if test="$value != ''"><xsl:value-of select="substring($value, 9, 2)"/></xsl:if></xsl:variable>
	<xsl:variable name="time"><xsl:if test="$value != ''"><xsl:value-of select="substring($value, 12, 8)"/></xsl:if></xsl:variable>

	<xsl:variable name="functionName">
		<xsl:choose>
			<xsl:when test="$hasTime != 'textDatetime'">openDateFlash</xsl:when>
			<xsl:otherwise>openDatetimeFlash</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	
	<input type="text" class="ocp_forma" style="width:28px;">
		<xsl:attribute name="name"><xsl:value-of select="$name"/>_dd</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="$dd"/></xsl:attribute>
	</input>
	/
	<input type="text" class="ocp_forma" style="width:28px;">
		<xsl:attribute name="name"><xsl:value-of select="$name"/>_mm</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="$mm"/></xsl:attribute>
	</input>
	/
	<input type="text" class="ocp_forma" style="width:40px;">
		<xsl:attribute name="name"><xsl:value-of select="$name"/>_yyyy</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="$yyyy"/></xsl:attribute>
	</input>
	<xsl:if test="($hasTime = 'textDatetime') or ($hasTime = 'true')">
		<input type="text" class="ocp_forma" style="width:60px; margin-left: 3px">
			<xsl:attribute name="name"><xsl:value-of select="$name"/>_time</xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="$time"/></xsl:attribute>
		</input>
	</xsl:if>
	<a href="javascript: void(0);">
		<xsl:attribute name="onClick"><xsl:value-of select="$functionName"/>(event,'formObject.<xsl:value-of select="$name"/>'); return false;</xsl:attribute>
		<img src="/ocp/img/opsti/kontrole/kontrola_kalendar.gif" class="ocp_kontrola">
			<xsl:attribute name="title"><xsl:value-of select="$labCalendar"/></xsl:attribute>
		</img>
	</a>
</xsl:template>
</xsl:stylesheet> 