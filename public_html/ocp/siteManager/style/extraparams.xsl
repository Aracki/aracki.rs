<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="templates_named.xsl"/>
<xsl:import href="../../config/forms_config_sm.xsl"/>
<xsl:output method="html"/>
<xsl:template match="type">
	<xsl:variable name="deca"><xsl:value-of select="count(./*)"/></xsl:variable>
	<table class="ocp_opcije_table" border="0"> 
		<tr> 
			<td class="ocp_opcije_td_ikona">
				<xsl:attribute name="rowspan"><xsl:value-of select="$deca+1"/></xsl:attribute>
				<img src="/ocp/img/opsti/opcije/ikone/ikona_dodatni_parametri.gif">
					<xsl:attribute name="title"><xsl:value-of select="./@Title"/></xsl:attribute>
				</img>
			</td> 
			<td colspan="2" class="ocp_opcije_td_naslov"><xsl:value-of select="./@Title"/></td> 
		</tr>
		<xsl:for-each select="./*">
			<xsl:variable name="namePolja"><xsl:value-of select="name()"/></xsl:variable>
			<xsl:variable name="necessary">
				<xsl:choose>
					<xsl:when test="contains(@validate, 'is_necessary')">true</xsl:when>
					<xsl:otherwise>false</xsl:otherwise>
				</xsl:choose>
			</xsl:variable>
			<tr>
				<xsl:if test="@inputType != 'html-editor'">
					<td class="ocp_opcije_td" style="width:22%">
						<span class="ocp_opcije_tekst1"><xsl:value-of select="@label"/></span>
						<xsl:if test="$necessary = 'true'"><span class="ocp_opcije_obavezno">*</span></xsl:if>
					</td>
				</xsl:if>
				<td class="ocp_opcije_td">
					<xsl:if test="@inputType = 'html-editor'">
						<xsl:attribute name="colspan">2</xsl:attribute>
					</xsl:if>
					<xsl:call-template name="polje">
						<xsl:with-param name="imePolja" select="$namePolja"/>
						<xsl:with-param name="tipPolja" select="@inputType"/>
						<xsl:with-param name="vrednostPolja" select="."/>
						<xsl:with-param name="dirPretrage" select="@root"/>
						<xsl:with-param name="sirinaPolja" select="@width"/>
						<xsl:with-param name="visinaPolja" select="@height"/>
						<xsl:with-param name="sveVrednosti" select="@allvalues"/>
						<xsl:with-param name="sveLabele" select="@alllabels"/>
						<xsl:with-param name="straId" select="../@Stra_Id"/>
						<xsl:with-param name="podtip" select="@podtip"/>
						<xsl:with-param name="restrict" select="@where"/>
						<xsl:with-param name="startIndex" select="@startIndex"/>
						<xsl:with-param name="value_label" select="@value_label"/>
						<xsl:with-param name="labCalendar" select="../@labCalendar"/>
						<xsl:with-param name="labCreateLinkOnPage" select="../@labCreateLinkOnPage"/>
						<xsl:with-param name="labCreateLinkOnBlock" select="../@labCreateLinkOnBlock"/>
						<xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
						<xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
						<xsl:with-param name="labSelectedLinkPreview" select="../@labSelectedLinkPreview"/>
						<xsl:with-param name="labRichTextFormat" select="../@labRichTextFormat"/>
						<xsl:with-param name="labColorPallete" select="../@labColorPallete"/>
						<xsl:with-param name="labSelect" select="../@labSelect"/>
					</xsl:call-template>
				</td>
			</tr>
		</xsl:for-each>
	</table>
</xsl:template>
</xsl:stylesheet> 