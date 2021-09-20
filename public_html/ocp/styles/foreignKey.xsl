<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method="html"/>

<xsl:template name="foreignKey">
	<xsl:param name="name"/>
	<xsl:param name="type"/>
	<xsl:param name="value"/>
	<xsl:param name="restrict"/>
	<xsl:param name="chooseLabels"/>
	<xsl:param name="delimiter"/>
	<xsl:param name="to-be-divided"/>
	<xsl:param name="start"/>
	<xsl:param name="selName"/>
	<xsl:param name="counter"/>
	
	<table class="ocp_uni_table">
		<tr>
			<xsl:if test="$chooseLabels != ''">
				
				<td class="ocp_opcije_tekst1">
					<select class="ocp_forma" style="width:100px">
						<xsl:attribute name="Id">choose<xsl:value-of select="$name"/></xsl:attribute>
						<xsl:attribute name="onChange">changeSelect(null, '<xsl:value-of select="$type"/>',  '<xsl:value-of select="$name"/>', '<xsl:value-of select="$value"/>', this.options[this.selectedIndex].value, '<xsl:value-of select="$restrict"/>');</xsl:attribute>
						<option value="" > </option>
						<xsl:call-template name="divideChooseForeign">
							<xsl:with-param name="to-be-divided" select="$chooseLabels"/>
							<xsl:with-param name="delimiter" select="$delimiter"/>
							<xsl:with-param name="start" select="$start"/>
							<xsl:with-param name="selName" select="$name"/>
							<xsl:with-param name="counter" select="1"/>
						</xsl:call-template>
					</select>
				</td>						
			</xsl:if>
			<td class="ocp_opcije_tekst1">
				<input type="hidden">
					<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="$value"/></xsl:attribute>
				</input>
				<iframe frameborder="0" scrolling="no" height="20" width="100%">
					<xsl:attribute name="src">/ocp/styles/foreignKey.php?random=<xsl:value-of select="../@random"/>&amp;Type=<xsl:value-of select="$type"/>&amp;FieldName=<xsl:value-of select="$name"/>&amp;FieldValue=<xsl:value-of select="$value"/>&amp;offset=<xsl:value-of select="$start"/>&amp;restrict=<xsl:value-of select="$restrict"/></xsl:attribute>
					<xsl:attribute name="name">frame<xsl:value-of select="$name"/></xsl:attribute>
					<xsl:attribute name="id">subForm_<xsl:value-of select="@SubType"/></xsl:attribute>
					<img src="/ocp/img/blank.gif" width="1"/>
				</iframe>

				<xsl:if test="inputType='upload'">	
					<a>
						<xsl:attribute name="href">javascript:urlId=document.formObject.<xsl:value-of select="$name"/>.value;x = window.open('/ocp/dbUpload/dbdown.php?random=<xsl:value-of select="../@random"/>&amp;DownID='+urlId, '', 'width=500, height=400, resizable, scrollbars' ); x.focus();</xsl:attribute>
						<img src="/ocp/img/view.gif" border="0" alt="Proveri upload"/>
					</a>
				</xsl:if>
			</td>
		</tr>
	</table>
 </xsl:template>

<xsl:template name="divideChooseForeign">
	<xsl:param name="to-be-divided"/>
	<xsl:param name="delimiter"/>
	<xsl:param name="start"/>
	<xsl:param name="selName"/>
	<xsl:param name="counter"/>
		
	<xsl:choose>
	<xsl:when test="contains($to-be-divided,$delimiter)">
		<option>
			<xsl:attribute name="value"><xsl:value-of select='$counter'/></xsl:attribute>
			<xsl:if test="$counter = $start">
				<xsl:attribute name="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select='substring-before($to-be-divided,$delimiter)'/>
		</option>
		<xsl:call-template name="divideChooseForeign">
			<xsl:with-param name="to-be-divided" select="substring-after($to-be-divided,$delimiter)"/>
			<xsl:with-param name="delimiter" select="'|@$'"/>
			<xsl:with-param name="start" select="$start"/>
			<xsl:with-param name="selName" select="$selName"/>
			<xsl:with-param name="counter" select="$counter+1"/>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<option>
			<xsl:attribute name="value"><xsl:value-of select='$counter'/></xsl:attribute>
			<xsl:if test="$counter = $start">
				<xsl:attribute name="selected"></xsl:attribute>
			</xsl:if>
			<xsl:value-of select='$to-be-divided'/>
		</option>
	</xsl:otherwise>
	</xsl:choose>
 </xsl:template>

 </xsl:stylesheet> 