<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="imgPosition">
	<xsl:param name="name"/>
	<xsl:param name="value"/>
	<xsl:param name="default"/>
		<input type="hidden">
			<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
		</input>
		<xsl:variable name="selected">
			<xsl:choose>
				<xsl:when test="($value = '') and ($default != '')">
					<xsl:value-of select="$default"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$value"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:if test="($value = '') and ($default != '')">
			<script>document.formObject.<xsl:value-of select="$name"/>.value=<xsl:value-of select="$default"/></script>
		</xsl:if>
		<table class="ocp_uni_table" style="width:70%">
		<tr>
			<td style="white-space: nowrap;">
				<input type="radio" name="positionRG" value="1">
					<xsl:if test="$selected = '1'">
						<xsl:attribute name="checked"></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="onChange">document.formObject.<xsl:value-of select="$name"/>.value='1'</xsl:attribute>
				</input>
				<img src="/ocp/img/opsti/opcije/ikone/polozaj_slike_1.gif" width="23" height="20" align="absbottom"/>
			</td>
			<td style="white-space: nowrap;">
				<input type="radio" name="positionRG" value="2">
					<xsl:if test="$selected = '2'">
						<xsl:attribute name="checked"></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="onChange">document.formObject.<xsl:value-of select="$name"/>.value='2'</xsl:attribute>
				</input>
				<img src="/ocp/img/opsti/opcije/ikone/polozaj_slike_2.gif" width="23" height="20" align="absbottom"/>
			</td>
			<td style="white-space: nowrap;">
				<input type="radio" name="positionRG" value="3">
					<xsl:if test="$selected = '3'">
						<xsl:attribute name="checked"></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="onChange">document.formObject.<xsl:value-of select="$name"/>.value='3'</xsl:attribute>
				</input>
				<img src="/ocp/img/opsti/opcije/ikone/polozaj_slike_3.gif" width="23" height="20" align="absbottom"/>
			</td>
			<td style="white-space: nowrap;">
				<input type="radio" name="positionRG" value="4">
					<xsl:if test="$selected = '4'">
						<xsl:attribute name="checked"></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="onChange">document.formObject.<xsl:value-of select="$name"/>.value='4'</xsl:attribute>
				</input>
				<img src="/ocp/img/opsti/opcije/ikone/polozaj_slike_4.gif" width="23" height="20" align="absbottom"/>
			</td>
		</tr>
	</table>
 </xsl:template>

  </xsl:stylesheet> 