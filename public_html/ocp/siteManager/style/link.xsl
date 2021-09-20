<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:template match="import[@type='link']">
<xsl:variable name="nameLinka">
	<xsl:value-of select="@name"/>
</xsl:variable>
<table class="ocp_opcije_table">
	<tr>
		<td rowspan="5"  class="ocp_opcije_td_ikona">
			<img src="/ocp/img/opsti/opcije/ikone/ikona_link_na_slici.gif">
				<xsl:attribute name="title"><xsl:value-of select="@label"/></xsl:attribute>
			</img>
		</td>
		<td colspan="2" class="ocp_opcije_td_naslov"><xsl:value-of select="@label"/></td>
	</tr>
	<tr>
		<td  class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./urlLinka/@label"/></span>
		</td>
		<td class="ocp_opcije_td">
			<table class="ocp_uni_table">
			<tr>
				<td class="ocp_dugmici_td_levi">
					<xsl:call-template name="intLink">
						<xsl:with-param name="style">width:<xsl:value-of select="$link-field-width"/>px;</xsl:with-param>
						<xsl:with-param name="value"><xsl:value-of select="./urlLinka"/></xsl:with-param>
						<xsl:with-param name="name"><xsl:value-of select="concat($nameLinka,'urlLinka')"/></xsl:with-param>
						<xsl:with-param name="straId" select="../@Stra_Id"/>
						<xsl:with-param name="labCreateLinkOnPage" select="../@labCreateLinkOnPage"/>
						<xsl:with-param name="labCreateLinkOnBlock" select="../@labCreateLinkOnBlock"/>
						<xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
						<xsl:with-param name="labSelectedLinkPreview" select="../@labSelectedLinkPreview"/>
						<xsl:with-param name="dirPretrage" select="./urlLinka/@root"/>
					</xsl:call-template>					
					
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td  class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./labela/@label"/></span>
		</td>
		<td class="ocp_opcije_td">
			<xsl:call-template name="textArea">
				<xsl:with-param name="style">width:<xsl:value-of select="$text-area-width"/>px; height:<xsl:value-of select="$text-area-height"/>px;</xsl:with-param>
				<xsl:with-param name="value"><xsl:value-of select="./labela"/></xsl:with-param>
				<xsl:with-param name="name" select="concat($nameLinka,'labela')"/>
			</xsl:call-template>
		</td>
	</tr>
</table>
</xsl:template>
</xsl:stylesheet> 