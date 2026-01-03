<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:preserve-space elements="text"/>
<xsl:output method="html"/>

<xsl:template match="Root">

<xsl:if test="./@action">
	<xsl:for-each select="./actions/action[contains(@place,'objectlist')]">
		<script src="/ocp/jscript/actions/{@url}.js" type="text/javascript"><img src="/ocp/img/blank.gif" width="0"/></script>
	</xsl:for-each>


	<div id="ocp_main_edit_bloka_table">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<div id="ocp_blok_menu_1">
						<table class="ocp_blokovi_table">
							<tr>
								<td class="ocp_blokovi_td" style="padding-left: 6px;">
									<xsl:value-of select="@labHeader"/>
								</td>
								<td class="ocp_blokovi_td" style="text-align: right;">
									<xsl:if test="@labFilter != ''">
										<span style="color: #C42E00;"><xsl:value-of select="@labFilter"/></span>
										<xsl:value-of select="@filterText" disable-output-escaping="yes"/>
									</xsl:if>
								</td>
							</tr>
						</table>
					</div>
					<xsl:if test="count(./*[1]) > 0">
						<div id="stickyHeaderDiv" style="overflow:auto;">
							<table class="ocp_opcije_table" id="listTable" name="listTable" style="width:100%">
								<tr id="trHeader" style="position:relative; top:0px">
									<td valign="top" style="width:50px; white-space: nowrap;margin: 0px; padding: 0px;">
									<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td class="ocp_opcije_td_header" align="right" style="white-space: nowrap;">
													<span class="ocp_opcije_tekst3"><xsl:value-of select="@labNo"/></span>
												</td>
											</tr>
										</table></td>
		
			<xsl:for-each select="./fields[1]/field">
				<xsl:if test="(name != 'Id') or (name = 'Id' and ../../@idIden = '1')">
					<xsl:call-template name="sortKolona">
						<xsl:with-param name="labela" select="@label"/>
						<xsl:with-param name="name" select="name"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
			<xsl:for-each select="./actions/action[contains(@place,'objectlist')]">
				<xsl:if test="@image = '' and @label != ''">
					<xsl:call-template name="actionKolona">
						<xsl:with-param name="labela" select="@label"/>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>

									<td valign="top" style="white-space:nowrap;margin: 0px; padding: 0px;"><table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td class="ocp_opcije_td_header" style="white-space:nowrap;">
													<span class="ocp_opcije_tekst3"><xsl:value-of select="@labTools"/></span>
												</td>
											</tr>
										</table></td>
								</tr>
								<xsl:apply-templates/>
							</table>
						</div>						
					</xsl:if>
				</td>
			</tr>
		</table>
	</div>
</xsl:if>
</xsl:template>

<xsl:template match="fields">
	<xsl:variable name="count"><xsl:value-of select="Count + ../@startIndex"/></xsl:variable>

	<xsl:variable name="uid1">
		<xsl:for-each select="./*">
			<xsl:if test="./name ='Id'"><xsl:value-of select="./value"/></xsl:if>
		</xsl:for-each>
	</xsl:variable>

	<xsl:variable name="type"><xsl:value-of select="../@type"/></xsl:variable>

	<tr style="cursor:pointer;">
		<xsl:attribute name="onclick">if (!pressed) {goForm('<xsl:value-of select="$uid1"/>', 'iu');}	pressed = false;</xsl:attribute>

		<td class="ocp_opcije_td" align="right">
			<xsl:if test="position() = 1"><xsl:attribute name="style">border-top:0px;</xsl:attribute></xsl:if>
			<span class="ocp_opcije_tekst1" ><xsl:value-of select="$count + 1"/>.</span>
		</td>

		<xsl:apply-templates/>

		<xsl:for-each select="../actions/action[contains(@place,'objectlist')]">
			<xsl:if test="@image = '' and @label != ''">
				<td class="ocp_opcije_td" align="right">
					<a href="#" onclick="pressed=true; {@url}('{$type}', '{$uid1}'); return false;" class="ocp_opcije_tekst1"><xsl:value-of select="@label"/></a>
				</td>
			</xsl:if>
		</xsl:for-each>
		
		<xsl:if test="../@action='objects.php'">
			<xsl:variable name="width"><xsl:value-of select="80+count(../actions/action[@image != '' and contains(@place,'objectlist')])*25"/></xsl:variable>
			<td class="ocp_opcije_td_forma" width="{$width}" align="center">
				<xsl:if test="position() = 1"><xsl:attribute name="style">border-top:0px;</xsl:attribute></xsl:if>
				<img src="/ocp/img/opsti/kontrole/kontrola_edituj_objekat.gif" border="0" width="20" height="21">
					<xsl:attribute name="onclick">goForm('<xsl:value-of select="$uid1"/>','iu');pressed=true;</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="../@labEditObject"/></xsl:attribute>
				</img>
				<xsl:if test="../@right='2' or ../@right='3' or ../@right='4'">
					<img src="/ocp/img/blank.gif" border="0"/>
					<img src="/ocp/img/opsti/kontrole/kontrola_kopiraj_objekat.gif" border="0"  width="20" height="21">
						<xsl:attribute name="onclick">goForm('<xsl:value-of select="$uid1"/>', 'copy');pressed=true;</xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="../@labCopyObject"/></xsl:attribute>
					</img>
				</xsl:if>
				<xsl:for-each select="../actions/action[contains(@place,'objectlist')]">
					<xsl:if test="@image != ''">
						<img src="{@image}" border="0" onclick="{@url}('{$type}', '{$uid1}');pressed=true;"/>
					</xsl:if>
				</xsl:for-each>
				<xsl:if test="../@right='4'">
					<img src="/ocp/img/blank.gif" border="0"/>
					<img src="/ocp/img/opsti/kontrole/kontrola_obrisi_objekat.gif" border="0"  width="20" height="21">
						<xsl:attribute name="onclick">goDelete('<xsl:value-of select="$uid1"/>');pressed=true;</xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="../@labDeleteObject"/></xsl:attribute>
					</img>
				</xsl:if>
			</td>
		</xsl:if>
	</tr>
</xsl:template>

<xsl:template match="alt"/>
<xsl:template match="Count"/>

<xsl:template match="field">
	<xsl:if test="(name != 'Id') or (name = 'Id' and ../../@idIden = '1')">
		<td class="ocp_opcije_td">
			<xsl:if test="(1 + count(parent::*/preceding-sibling::*)) = 1"><xsl:attribute name="style">border-top:0px;</xsl:attribute></xsl:if>
			<xsl:choose>
				<xsl:when test="name != 'OcpOrderColumn'">
					<span class="ocp_opcije_tekst1">
						<xsl:if test="name = 'Id'">#</xsl:if><xsl:value-of select="value"/>
					</span>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="sortOrder">
						<xsl:with-param name="objId" select="../field[name = 'Id']/value"/>
						<xsl:with-param name="orderValue" select="value"/>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</td>			
	</xsl:if>

</xsl:template>

<!--TEMPLATES KOJI SU NAMED-->

<xsl:template name="sortOrder">
	<xsl:param name="objId"/>
	<xsl:param name="orderValue"/>

	<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
		<tr>
			<td style="white-space: nowrap;">
				<span class="ocp_opcije_tekst1"><xsl:value-of select="$orderValue"/></span>
				<img src="/ocp/img/blank.gif" width="3" border="0"/>
				<a href="#">
					<xsl:attribute name="href">javascript:goOcpOrder('<xsl:value-of select="$objId"/>','desc')</xsl:attribute>
					<img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_gore.gif" title="{../../@labMoveUp}"/>
				</a>
				<a>
					<xsl:attribute name="href">javascript:goOcpOrder('<xsl:value-of select="$objId"/>','asc')</xsl:attribute>
					<img border="0" height="7" src="/ocp/img/opsti/kontrole/strelica_filter_dole.gif" title="{../../@labMoveDown}"/>
				</a>
			</td>
		</tr>
	</table>
</xsl:template>

<xsl:template name="sortKolona">
	<xsl:param name="labela"/>
	<xsl:param name="name"/>

	<td valign="top" style="margin: 0px; padding: 0px;">
		<xsl:if test="$labela = 'ID'">
			<xsl:attribute name="width">20 px</xsl:attribute>
		</xsl:if>
		<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
			<tr>
	<td class="ocp_opcije_td_header">
		<xsl:attribute name="style">
			<xsl:choose>
				<xsl:when test="../../@sortName = $name">white-space: nowrap; border-bottom: 2px solid #C42E00;</xsl:when>
				<xsl:otherwise>white-space: nowrap;</xsl:otherwise>
			</xsl:choose>
		</xsl:attribute>
		<span class="ocp_opcije_tekst3"><xsl:value-of select="$labela"/></span><img src="/ocp/img/blank.gif" width="3" border="0"/>
		<a href="#">
			<xsl:attribute name="href">javascript:sort('<xsl:value-of select="$name"/>','asc')</xsl:attribute>
			<img border="0" height="7">
				<xsl:attribute name="src"><xsl:choose>
					<xsl:when test="../../@sortName = $name and ../../@direction = 'asc'">/ocp/img/opsti/kontrole/strelica_filter_gore_select.gif</xsl:when>
					<xsl:otherwise>/ocp/img/opsti/kontrole/strelica_filter_gore.gif</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="../../@labSortAscending"/></xsl:attribute>				
			</img>
		</a>
		<a>
			<xsl:attribute name="href">javascript:sort('<xsl:value-of select="$name"/>','desc')</xsl:attribute>
			<img border="0" height="7">
				<xsl:attribute name="src"><xsl:choose>
					<xsl:when test="../../@sortName = $name and ../../@direction='desc'">/ocp/img/opsti/kontrole/strelica_filter_dole_select.gif</xsl:when>
					<xsl:otherwise>/ocp/img/opsti/kontrole/strelica_filter_dole.gif</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="../../@labSortDescending"/></xsl:attribute>
			</img>
		</a>
	</td>
			</tr>
		</table></td>
</xsl:template>

<xsl:template name="actionKolona">
	<xsl:param name="labela"/>

	<td valign="top" style="margin: 0px; padding: 0px;">
		<table width="100%" cellpadding="0" cellspacing="0" style="margin: 0px; padding: 0px;">
			<tr>
				<td class="ocp_opcije_td_header" style="white-space: nowrap;">
					<span class="ocp_opcije_tekst3"><xsl:value-of select="$labela"/></span>
				</td>
			</tr>
		</table>
	</td>
</xsl:template>

</xsl:stylesheet> 