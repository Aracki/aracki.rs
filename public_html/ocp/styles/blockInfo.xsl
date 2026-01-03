<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="blockInfo">
	<table class="ocp_naslov_table">
		<tr><td class="ocp_naslov_td"><xsl:value-of select="@labBlockEditing"/>: </td></tr>
	</table>
	<table class="ocp_info_table"> 
		<tr> 
			<td class="ocp_info_td">
				<xsl:value-of select="@labBlockType"/>: 
				<xsl:if test="(@Blok_MetaNaziv != '') and (@Blok_MetaNaziv != 'undefined') and (@Blok_MetaNaziv != 'null')">
					<img src="/ocp/img/opsti/blokovi/ikone/srednji/deljeni.gif" class="ocp_mala_ikona"/>
					<img src="/ocp/img/blank.gif" width="5"/>
					<span class="ocp_info_bold"><xsl:value-of select="@Blok_MetaNaziv"/></span> 
					<img src="/ocp/img/blank.gif" width="5"/>
				</xsl:if>
				<img class="ocp_mala_ikona">
					<xsl:attribute name="src"><xsl:value-of select="@TipB_SlikaUrl"/></xsl:attribute>
				</img>
				<img src="/ocp/img/blank.gif" width="5"/>
				<span class="ocp_info_td_vrednost"><xsl:value-of select="@TipB_Naziv"/></span>
				<xsl:if test="@dinamic='1' and @type != ''">
					<img src="/ocp/img/blank.gif" width="5"/>
					<span class="ocp_info_td_vrednost"><xsl:value-of select="@labEditType"/><img src="/ocp/img/blank.gif" width="3" border="0"/>
					<a target="_blank">
						<xsl:attribute name="href">/ocp/object_frameset_popup.php?random=<xsl:value-of select="@random"/>&amp;objType=<xsl:value-of select="@type"/>&amp;ocpDefaultValues=<xsl:value-of select="@ocpDefaultValues"/></xsl:attribute>
						<xsl:value-of select="@type"/>
					</a><img src="/ocp/img/blank.gif" width="3"/>
					(<a target="_blank">
						<xsl:attribute name="href">/ocp/object_frameset_popup.php?random=<xsl:value-of select="@random"/>&amp;objType=<xsl:value-of select="@type"/></xsl:attribute>
						<xsl:value-of select="@labEditAll"/>
					</a>)
					</span>
				</xsl:if>
			</td>
		</tr> 
	</table>
</xsl:template>

 </xsl:stylesheet> 