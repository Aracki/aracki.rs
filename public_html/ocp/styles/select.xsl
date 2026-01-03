<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="select">
	<xsl:param name="style"/>
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
				<select class="ocp_forma">
					<xsl:attribute name="style">
						<xsl:value-of select="$style"/>
					</xsl:attribute>
					<xsl:attribute name="name">
						<xsl:value-of select="$name"/>
					</xsl:attribute>
					<option value=""></option>
					<xsl:call-template name="divideSelect">
						<xsl:with-param name="to-be-divided" select="$to-be-divided"/>
						<xsl:with-param name="labels" select="$labels"/>
						<xsl:with-param name="selected" select="$selected"/>
						<xsl:with-param name="delimiter" select="$delimiter"/>
					</xsl:call-template>
				</select>
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

 <xsl:template name="divideSelect">
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>
	<xsl:choose>
	<xsl:when test="contains($to-be-divided,$delimiter)">
		<option class="ocp_tekst2">
		<xsl:attribute name="value"><xsl:value-of select='substring-before($to-be-divided,$delimiter)'/></xsl:attribute>
		<xsl:if test="substring-before($to-be-divided,$delimiter) = $selected">
			<xsl:attribute name="selected"></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="substring-before($labels,$delimiter)"/>
		</option>
		<xsl:call-template name="divideSelect">
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="labels" select="substring-after($labels,$delimiter)"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="$delimiter"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<option class="ocp_tekst2">
		<xsl:attribute name="value"><xsl:value-of select='$to-be-divided'/></xsl:attribute>
		<xsl:if test="$to-be-divided = $selected">
			<xsl:attribute name="selected"></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="$labels"/>
		</option>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

 </xsl:stylesheet> 