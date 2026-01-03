<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:import href="../../siteManager/style/templates_named.xsl"/>
<xsl:import href="../../config/forms_config.xsl"/>
<xsl:output method="html"/>
<xsl:template match="parameters">
	<form name="formObject" id="formObject" method="get" onSubmit="return validate();" action="lower.php" target="detailFrame">
		<input type="hidden" name="reportId" value="{./@reportId}"/>
		<table class="ocp_naslov_table">
			<tr>
				<td class="ocp_naslov_td">
					<xsl:value-of select="./@title"/>
				</td>
			</tr>
		</table>
		<table class="ocp_opcije_table" border="0" cellpadding="0" cellspacing="0">
			<xsl:for-each select="./*">
				<xsl:variable name="namePolja"><xsl:value-of select="@name"/></xsl:variable>
				
				<xsl:choose>
					<xsl:when test="@inputType = 'hidden'">
						<input type="hidden" value="{.}" name="{$namePolja}"/>
					</xsl:when>
					<xsl:when test="@inputType = 'include'"><!--ako ima includes nodove-->
						<!--<tr> 
							<td style="padding:0px;" colspan="2">
								<table class="ocp_subforma_naslov_table">
									<tr>
										<td class="ocp_subforma_naslov_td"><xsl:value-of select="@label"/></td>
									</tr>
								</table>
							</td>
						</tr>-->
						<tr> 
							<td class="ocp_opcije_td" style="padding:0px;" colspan="2">
								<iframe frameborder="0" width="100%" scrolling="no">
									<xsl:attribute name="src"><xsl:value-of select="@url"/>?random=<xsl:value-of select="../@random"/>&amp;Label=<xsl:value-of select="@label"/></xsl:attribute>
									<xsl:attribute name="id">include_<xsl:value-of select="@label"/></xsl:attribute>	
								</iframe>
							</td>
						</tr>
					</xsl:when>
					<xsl:otherwise>

				<xsl:variable name="necessary">
					<xsl:choose>
						<xsl:when test="contains(@validate, 'is_necessary')">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>

				<tr>
					<td class="ocp_opcije_td" style="width:22%">
						<span class="ocp_opcije_tekst1"><xsl:value-of select="@label"/></span>
						<xsl:if test="$necessary = 'true'"><span class="ocp_opcije_obavezno">*</span></xsl:if>
					</td>
					<td class="ocp_opcije_td">
						<xsl:call-template name="polje">
							<xsl:with-param name="imePolja" select="$namePolja"/>
							<xsl:with-param name="tipPolja" select="@inputType"/>
							<xsl:with-param name="vrednostPolja" select="."/>
							<xsl:with-param name="sveVrednosti" select="@allvalues"/>
							<xsl:with-param name="sveLabele" select="@alllabels"/>
							<xsl:with-param name="podtip" select="@podtip"/>
							<xsl:with-param name="restrict" select="@where"/>
							<xsl:with-param name="startIndex" select="@startIndex"/>
							<xsl:with-param name="labCalendar" select="../@labCalendar"/>
							<xsl:with-param name="labSelect" select="../@labSelect"/>
						</xsl:call-template>
					</td>
				</tr>

					</xsl:otherwise>
				</xsl:choose>

			</xsl:for-each>
		</table>

		<table width="100%">
			<tr>
				<td height="40" align="center" class="ocp_text">
					<input type="submit" name="submit2" class="ocp_dugme" value="{@labSearch}" id="searchButton"/>
					<img src="/ocp/img/blank.gif" width="3"/>
					<input type="button" name="button" class="ocp_dugme"  value="{@labCancel}" id="cancelButton" onclick="document.formObject.reset();"/>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>
</xsl:stylesheet> 