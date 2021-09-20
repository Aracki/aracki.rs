<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="html"/>

<xsl:template name="divSlikaTemplate">
	<xsl:param name="url"/>
	<xsl:param name="okvir"/>
	<xsl:param name="width"/>
	<xsl:param name="height"/>
	<xsl:param name="sign"/>
	<xsl:param name="alt"/>
	<xsl:param name="align"/>
	<xsl:param name="link"/>

	<div>
		<xsl:attribute name="class">image_holder <xsl:value-of select="$align"/></xsl:attribute>
		<xsl:choose>
			<xsl:when test="$link != ''">
				<xsl:variable name="target">
					<xsl:choose>
						<xsl:when test="contains($link, 'http')">_blank</xsl:when>
						<xsl:otherwise>_self</xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="$link"/>
					</xsl:attribute>
					<xsl:attribute name="target">
						<xsl:value-of select="$target"/>
					</xsl:attribute>
	
					<xsl:call-template name="slikaTemplate">
						<xsl:with-param name="url" select="$url"/>
						<xsl:with-param name="okvir" select="$okvir"/>
						<xsl:with-param name="width" select="$width"/>
						<xsl:with-param name="height" select="$height"/>
						<xsl:with-param name="sign" select="$sign"/>
						<xsl:with-param name="alt" select="$alt"/>
					</xsl:call-template>
				</a>
			</xsl:when>
			<xsl:otherwise>
					<xsl:call-template name="slikaTemplate">
						<xsl:with-param name="url" select="$url"/>
						<xsl:with-param name="okvir" select="$okvir"/>
						<xsl:with-param name="width" select="$width"/>
						<xsl:with-param name="height" select="$height"/>
						<xsl:with-param name="sign" select="$sign"/>
						<xsl:with-param name="alt" select="$alt"/>
					</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="$sign != ''">
			<p>
				<xsl:if test="$width != ''">
					<xsl:attribute name="style">width:<xsl:value-of select="$width"/>px;</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="$sign" disable-output-escaping="yes"/>
			</p>	
		</xsl:if>
	</div>
</xsl:template>

<xsl:template name="slikaTemplate">
	<xsl:param name="url"/>
	<xsl:param name="okvir"/>
	<xsl:param name="width"/>
	<xsl:param name="height"/>
	<xsl:param name="alt"/>

	<xsl:choose>
		<xsl:when test="contains($url, '.swf') or contains($url, '.flv') or contains($url, 'youtube.com')">
			<xsl:call-template name="flashTemplate">
				<xsl:with-param name="url" select="$url"/>
				<xsl:with-param name="width" select="$width"/>
				<xsl:with-param name="height" select="$height"/>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<img src="{$url}" alt="{$alt}">
				<xsl:if test="$okvir = '1'">
					<xsl:attribute name="class">image_border</xsl:attribute>
				</xsl:if>
				<xsl:if test="$width != ''">
					<xsl:attribute name="width">
						<xsl:value-of select="$width"/>
					</xsl:attribute>
				</xsl:if>
				<xsl:if test="$height != ''">
					<xsl:attribute name="height">
						<xsl:value-of select="$height"/>
					</xsl:attribute>
				</xsl:if>
			</img>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="flashTemplate">
	<xsl:param name="url"/>
	<xsl:param name="width"/>
	<xsl:param name="height"/>

	<xsl:variable name="id">flash<xsl:value-of select="/blok/@id"/></xsl:variable>

	<xsl:choose>
		<xsl:when test="contains($url, 'youtube.com')">
			<object width="425" height="344">
				<param name="movie">
					<xsl:attribute name="value">
						<xsl:value-of select="$url"/>
					</xsl:attribute>
				</param>
				<param name="allowFullScreen" value="true"></param>
				<embed type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344">
					<xsl:attribute name="src">
						<xsl:value-of select="$url"/>
					</xsl:attribute>
				</embed>
			</object>
		</xsl:when>
		<xsl:otherwise>
			<div>
				<xsl:if test="contains($url, '.swf') or contains($url, 'youtube.com')"><xsl:attribute name="style">width: <xsl:value-of select="$width"/>px; height: <xsl:value-of select="$height"/>px; color: #fff;</xsl:attribute></xsl:if>
				<xsl:if test="contains($url, '.flv')"><xsl:attribute name="style">color: #fff;</xsl:attribute></xsl:if>
				<xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>&amp;nbsp;
			</div>
			<script type="text/javascript">
				window.setTimeout(displayFlash, 1);
				<xsl:choose>
					<xsl:when test="contains($url, '.swf') or contains($url, 'youtube.com')">
						function displayFlash() {
						   var so = new SWFObject("<xsl:value-of select="$url"/>", "<xsl:value-of select="$id"/>", "<xsl:value-of select="$width"/>", "<xsl:value-of select="$height"/>", "6", "#ffffff", "transparent", "wmode='opaque'");
						   so.write("<xsl:value-of select="$id"/>");
						}
					</xsl:when>
					<xsl:otherwise>
						function displayFlash() {
						   var so = new SWFObject("/images/flash/player.swf?movurl=<xsl:value-of select="$url"/>", "<xsl:value-of select="$id"/>", "320", "240", "6", "#ffffff", "transparent", "wmode='opaque'");
						   so.write("<xsl:value-of select="$id"/>");
						}
					</xsl:otherwise>
				</xsl:choose>
			</script>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>