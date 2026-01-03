<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:template match="asset/fields">
	<form name="formObject" id="formObject" method="post">
		<xsl:attribute name="action">subforms.php?random=<xsl:value-of select="./@random"/></xsl:attribute>

		<input name="Type" type="hidden" value="{@type}"/>
		<input name="TypeField" type="hidden" value="{@TypeField}"/>
		<input name="Editable" type="hidden" value="{@Editable}"/>
		<input name="SuperType" type="hidden" value="{@SuperType}"/>
		<input name="SuperTypeId" type="hidden" value="{@SuperTypeId}"/>
		<input name="ocp_brojac" type="hidden" value="{@ocp_brojac}"/>
		<input name="sortName" type="hidden" value="{@sortName}"/>
		<input name="direction" type="hidden" value="{@direction}"/>

		<table class="ocp_naslov_table">
			<tr>
				<td class="ocp_naslov_td"><xsl:value-of select="./@labHeader"/></td>
			</tr>
		</table>
		<table class="ocp_opcije_table">
			<xsl:apply-templates/>
		</table>	

		<table width="100%">
			<tr>
				<td height="40" align="center" class="ocp_text">
					<input type="button" name="submitConfirm" class="ocp_dugme" onclick="reconstruct();">
						<xsl:attribute name="value"><xsl:value-of select="./@labConfirm"/></xsl:attribute>
					</input>
				</td>
			</tr>
		</table>
	</form>
</xsl:template>

<xsl:template match="field[./inputType != 'hidden']">
	
	<tr>
		<xsl:call-template name="firstCell">
			<xsl:with-param name="label" select="label"/>
		</xsl:call-template>
		
		<td class="ocp_opcije_td">
			<span class="ocp_opcije_tekst2">
				<xsl:choose>
					<xsl:when test="@import != ''">
						<iframe width="100%" height="18" frameborder="0" scrolling="no">
							<xsl:attribute name="src"><xsl:value-of select="@import"/></xsl:attribute>
						</iframe>
					</xsl:when>

					<xsl:when test="(inputType='textBox') or (inputType='textarea') or (inputType='textDate') or (inputType='color') or (inputType='labela') or (inputType='file') or (inputType='image') or (inputType='fileImage') or (inputType='html-editor')">
						<xsl:value-of select="value"/>
					</xsl:when>

					<xsl:when test="inputType='check'">
						<xsl:choose>
							<xsl:when test="value='1' or value='true'"><xsl:value-of select="../@labYes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="../@labNo"/></xsl:otherwise>
						</xsl:choose>
					</xsl:when>

					<xsl:when test="inputType='radio' or inputType='select' or inputType='complex'">
						<xsl:call-template name="divideLabels">
							 <xsl:with-param name="to-be-divided" select="allvalues"/>
							 <xsl:with-param name="labels" select="alllabels"/>
							 <xsl:with-param name="selected" select="value"/>
							 <xsl:with-param name="delimiter" select="'|@$'"/>
						</xsl:call-template>
					</xsl:when>

					<xsl:when test="(inputType='complex'or inputType='upload')">
						<xsl:call-template name="divideLabels">
							<xsl:with-param name="to-be-divided" select="chooseLabels"/>
							<xsl:with-param name="labels" select="chooseLabels"/>
							 <xsl:with-param name="selected" select="value"/>
							<xsl:with-param name="delimiter" select="'|@$'"/>
						</xsl:call-template>
					</xsl:when>
					
					<xsl:otherwise/>
				</xsl:choose>
			</span>
		</td>
	</tr>
</xsl:template>

<xsl:template match="field[./inputType = 'hidden']"/>

<!--TEMPLATES KOJI SU NAMED-->

<xsl:template name="divideLabels">
	<xsl:param name="to-be-divided"/>
	<xsl:param name="labels"/>
	<xsl:param name="selected"/>
	<xsl:param name="delimiter"/>
	<xsl:choose>
	<xsl:when test="contains($to-be-divided,$delimiter)">
		<xsl:if test="substring-before($to-be-divided,$delimiter) = $selected">
			<xsl:value-of select="substring-before($labels,$delimiter)"/>	
		</xsl:if>
		<xsl:call-template name="divideLabels">
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="labels" select="substring-after($labels,$delimiter)"/>
			<xsl:with-param name="selected" select="$selected"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:if test="$to-be-divided = $selected">
			<xsl:value-of select="$labels"/>
		</xsl:if>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

  <xsl:template name="firstCell">
	<xsl:param name="label"/>
	
	<td class="ocp_opcije_td" style="width:22%">
		<span class="ocp_opcije_tekst1"><xsl:value-of select="$label"/></span>
	</td>
 </xsl:template>


</xsl:stylesheet> 