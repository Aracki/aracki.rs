<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="radio">
	<xsl:param name="name"/>
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>
	<xsl:param name="editLink"/>
	<xsl:param name="showEditLink"/>
	<xsl:param name="labUpdateListOfValue"/>

	<table class="ocp_uni_table">
		<tr>
			<td class="ocp_dugmici_td_levi" width="100%">
				<xsl:call-template name="divideRadio">
					<xsl:with-param name="name" select="$name"/>
					<xsl:with-param name="to-be-divided" select="$to-be-divided"/>
					<xsl:with-param name="labels" select="$labels"/>
					<xsl:with-param name="selected" select="$selected"/>
					<xsl:with-param name="delimiter" select="$delimiter"/>
				</xsl:call-template>
			</td>
<!--			<xsl:if test="$showEditLink = 'true'">
			<td class="ocp_dugmici_td_desni_3">
				<a href="javascript:void(0);">
					<xsl:attribute name="onClick">javascript:window.open('<xsl:value-of select="$editLink"/>', 'listaFrame', 'width=625, height=400, scrollbars=yes');return false;</xsl:attribute>
					<img src="/ocp/img/opsti/kontrole/kontrola_nova_lista.gif" border="0" align="right">
						<xsl:attribute name="title"><xsl:value-of select="labUpdateListOfValue"/></xsl:attribute>
					</img>
				</a>
			</td>
			</xsl:if>-->
			
		</tr>
	</table>

 </xsl:template>

 <xsl:template name="divideRadio">
	<xsl:param name="name"/>
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>

	<xsl:choose>
	<xsl:when test="contains($to-be-divided, $delimiter)">
		<input type="radio" class="ocp_tekst2">
			<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select='substring-before($to-be-divided,$delimiter)'/></xsl:attribute>
			<xsl:if test="substring-before($to-be-divided,$delimiter) = $selected"><xsl:attribute name="checked"></xsl:attribute></xsl:if>
		</input>
		<span class="ocp_opcije_tekst2" style="margin-right: 3px"><xsl:value-of select="substring-before($labels,$delimiter)"/></span>
		<xsl:call-template name="divideRadio">
			<xsl:with-param name="name" select="$name"/>
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="labels" select="substring-after($labels,$delimiter)"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="$delimiter"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<input type="radio" class="ocp_tekst2">
			<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select='$to-be-divided'/></xsl:attribute>
			<xsl:if test="$to-be-divided = $selected"><xsl:attribute name="checked"></xsl:attribute></xsl:if>
		</input>
		<span class="ocp_opcije_tekst2"><xsl:value-of select="$labels"/></span>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

 </xsl:stylesheet> 