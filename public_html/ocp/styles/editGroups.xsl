<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="editGroups">
	<xsl:param name="editGroups"/>
	<xsl:param name="editGroupsLabels"/>
	<xsl:param name="selected"/>

	<xsl:choose>
	<xsl:when test="contains($editGroups,'|')">
		<xsl:choose>
			<xsl:when test="substring-before($editGroups,'|') = $selected">
				<option>
					<xsl:attribute name="selected"></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="substring-before($editGroups,'|')"/></xsl:attribute>
					selected: <xsl:value-of select="substring-before($editGroupsLabels,'|')"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option>
					<xsl:attribute name="value"><xsl:value-of select="substring-before($editGroups,'|')"/></xsl:attribute>
					not selected: <xsl:value-of select="substring-before($editGroupsLabels,'|')"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:call-template name="editGroups">
			<xsl:with-param name="editGroups" select="substring-after($editGroups,'|')"/>
			<xsl:with-param name="editGroupsLabels" select="substring-after($editGroupsLabels,'|')"/>
			<xsl:with-param name="selected" select="$selected"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:choose>
			<xsl:when test="$editGroups = $selected">
				<option>
					<xsl:attribute name="value"><xsl:value-of select="$editGroups"/></xsl:attribute>
					<xsl:attribute name="selected"></xsl:attribute>
					<xsl:value-of select="$editGroupsLabels"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option>
					<xsl:attribute name="value"><xsl:value-of select="$editGroups"/></xsl:attribute>
					<xsl:value-of select="$editGroupsLabels"/>
				</option>	
			</xsl:otherwise>
		</xsl:choose>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>
 </xsl:stylesheet> 