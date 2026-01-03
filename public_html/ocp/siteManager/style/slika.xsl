<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>
<xsl:template match="import[@type='slika']">

<xsl:variable name="nameSlike">
	<xsl:value-of select="@name"/>
</xsl:variable>

<xsl:variable name="borderSlike">
	<xsl:choose>
		<xsl:when test="./border != ''"><xsl:value-of select="./border"/></xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose>
</xsl:variable>
<table class="ocp_opcije_table"> 
	<tr> 
		<td class="ocp_opcije_td_ikona" rowspan="9">
			<img src="/ocp/img/opsti/opcije/ikone/ikona_slika.gif">
				<xsl:attribute name="title"><xsl:value-of select="@label"/></xsl:attribute>
			</img>
		</td> 
		<td colspan="2" class="ocp_opcije_td_naslov"><xsl:value-of select="@label"/></td> 
	</tr> 
	<tr> 
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="../@labImagePreview"/></span>
		</td> 
		<td class="ocp_opcije_td">
			<xsl:variable name="src">
				<xsl:choose><xsl:when test="./urlSlike != ''"><xsl:value-of select="./urlSlike"/></xsl:when>
				<xsl:otherwise>/ocp/img/blank.gif</xsl:otherwise></xsl:choose>
			</xsl:variable>

			<xsl:choose>
				<xsl:when test="not(contains($src, '.flv') or contains($src, '.swf'))">
					<img border="1" style="cursor:pointer;" src="{$src}" id="{concat($nameSlike,'imgSlike')}">
						<xsl:if test="./urlSlike != ''">
							<xsl:attribute name="onClick">urlCont=document.formObject.<xsl:value-of select="concat($nameSlike,'urlSlike')"/>;window.open(urlCont.value, '', 'width=500, height=400, resizable, scrollbars' );</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="onLoad">
							if (this.width+"" == '0' &amp;&amp; this.height+"" == '0'){//ie
								this.height = 50;
							} else {//firefox
								if (this.width &gt;= this.height){
									this.width = Math.min(this.width, 50);
								} else {
									this.height = Math.min(this.height, 50);
								}
							}
						</xsl:attribute>
					</img>
				</xsl:when>
				<xsl:otherwise>
					<img border="1" style="cursor:pointer;" src="/ocp/img/kontrole/file_kontrola/swf.gif" id="{concat($nameSlike,'imgSlike')}">
						<xsl:if test="./urlSlike != ''">
							<xsl:attribute name="onClick">var urlCont=document.formObject.<xsl:value-of select="concat($nameSlike,'urlSlike')"/>; var x = window.open('/ocp/controls/fileControl/swf_popup.php?url='+urlCont.value+'&amp;sirina=<xsl:value-of select="./width"/>&amp;visina=<xsl:value-of select="./height"/>', '', 'width=500, height=400, resizable, scrollbars' ); x.focus();</xsl:attribute>
						</xsl:if>
					</img>
				</xsl:otherwise>
			</xsl:choose>
		</td> 
	</tr>
	<tr> 
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./urlSlike/@label"/></span><span class="ocp_opcije_obavezno">*</span>
		</td> 
		<td class="ocp_opcije_td">
		<xsl:call-template name="fileImage">
			<xsl:with-param name="style">width:<xsl:value-of select="$file-field-width"/>px;</xsl:with-param>
			<xsl:with-param name="name"><xsl:value-of select="concat($nameSlike,'urlSlike')"/></xsl:with-param>
			<xsl:with-param name="value"><xsl:value-of select="./urlSlike"/></xsl:with-param>
			<xsl:with-param name="width"><xsl:value-of select="./width/@max"/></xsl:with-param>
			<xsl:with-param name="height"><xsl:value-of select="./height/@max"/></xsl:with-param>
			<xsl:with-param name="labBrowseServer" select="../@labBrowseServer"/>
			<xsl:with-param name="labSelectedImagePreview" select="../@labSelectedImagePreview"/>
			<xsl:with-param name="type" select="./urlSlike/@root"/>
			<xsl:with-param name="dirPretrage" select="./urlSlike/@root"/>
		</xsl:call-template>

		</td> 
	</tr> 
	<tr> 
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./signature/@label"/></span>
		</td> 
		<td class="ocp_opcije_td">
			<xsl:call-template name="textArea">
				<xsl:with-param name="style">width:<xsl:value-of select="$text-area-width"/>px; height:<xsl:value-of select="$text-area-height"/>px;</xsl:with-param>
				<xsl:with-param name="value"><xsl:value-of select="./signature"/></xsl:with-param>
				<xsl:with-param name="name" select="concat($nameSlike,'signature')"/>
			</xsl:call-template>
		</td>

	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./border/@label"/></span>
		</td>
		<td class="ocp_opcije_td">
			<xsl:variable name="selectedBorder"><xsl:choose>
				<xsl:when test="(./border = '') and (./border/@default != '')"><xsl:value-of select="./border/@default"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="./border"/></xsl:otherwise>
			</xsl:choose></xsl:variable>

			<xsl:call-template name="radio">
				<xsl:with-param name="name" select="concat($nameSlike,'border')"/>
				<xsl:with-param name="to-be-divided" select="./border/@allvalues"/>
				<xsl:with-param name="labels" select="./border/@alllabels"/>
				<xsl:with-param name="selected" select="$selectedBorder"/>
				<xsl:with-param name="delimiter" select="'|@$'"/>
				<xsl:with-param name="showEditLink" select="false"/>
			</xsl:call-template>
		</td>
	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="./alignment/@label"/></span>
		</td>
		<td class="ocp_opcije_td">
			<xsl:variable name="selectedAlign"><xsl:choose>
				<xsl:when test="(./alignment = '') and (./alignment/@default != '')"><xsl:value-of select="./alignment/@default"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="./alignment"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<xsl:call-template name="radio">
				<xsl:with-param name="name" select="concat($nameSlike,'alignment')"/>
				<xsl:with-param name="to-be-divided" select="./alignment/@allvalues"/>
				<xsl:with-param name="labels" select="./alignment/@alllabels"/>
				<xsl:with-param name="selected" select="$selectedAlign"/>
				<xsl:with-param name="delimiter" select="'|@$'"/>
				<xsl:with-param name="showEditLink" select="false"/>
			</xsl:call-template>
		</td>
	</tr>
	<tr>
		<td class="ocp_opcije_td" style="width:22%">
			<span class="ocp_opcije_tekst1"><xsl:value-of select="../@labDimension"/></span>
		</td>
		<td class="ocp_opcije_td">
			<span class="ocp_opcije_tekst2"><xsl:value-of select="./width/@label"/></span>
			<xsl:call-template name="textBox">
				<xsl:with-param name="style">width:<xsl:value-of select="$short-field-width"/>px; margin-left: 3px;</xsl:with-param>
				<xsl:with-param name="value" select="./width"/>
				<xsl:with-param name="name" select="concat($nameSlike,'width')"/>
			</xsl:call-template>
			<img src="/ocp/img/blank.gif" width="3"/>
			<span class="ocp_opcije_tekst2"><xsl:value-of select="./height/@label"/></span> 
			<xsl:call-template name="textBox">
				<xsl:with-param name="style">width:<xsl:value-of select="$short-field-width"/>px; margin-left: 3px;</xsl:with-param>
				<xsl:with-param name="value" select="./height"/>
				<xsl:with-param name="name" select="concat($nameSlike,'height')"/>
			</xsl:call-template>
		</td>
	</tr> 
</table> 
</xsl:template>
</xsl:stylesheet> 