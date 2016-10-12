<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:yahoo="urn:yahoo:travel" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ymaps="http://api.maps.yahoo.com/Maps/V1/AnnotatedMaps.xsd" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	<!-- root -->
	<!-- docs:  http://developer.yahoo.com/maps/simple/V1/reference.html -->
	<xsl:template match="yahoo:Result">
		<rss version="2.0">
			<channel>
				<title><xsl:value-of select=".//yahoo:Title"/></title>
			        <link>http://local.yahoo.com/collections?cid=<xsl:value-of select="./@id"/></link>
				<!-- blank for now -->
				<description><xsl:value-of select=".//yahoo:Description"/></description>
				<xsl:apply-templates select="yahoo:Item" mode="MapToitem"/>
			</channel>
		</rss>
	</xsl:template>
	<!--Item -->
	<xsl:template match="yahoo:Item" mode="MapToitem">
		<item>
			<title>
				<xsl:value-of select=".//yahoo:Title"/>
			</title>
			<link><xsl:value-of select=".//yahoo:Url"/></link>
			<description><xsl:value-of select=".//yahoo:Description"/></description>
		       <!--
			<description>
				<xsl:value-of select=".//yahoo:Title"/>
			</description>
			-->
			<ymaps:Address>
				<xsl:value-of select=".//yahoo:Address//yahoo:Address1"/>
				<xsl:value-of select=".//yahoo:Address//yahoo:Address2"/>
			</ymaps:Address>
			<ymaps:CityState>
				<xsl:value-of select=".//yahoo:Address//yahoo:City"/>, <xsl:value-of select=".//yahoo:Address//yahoo:State"/>
			</ymaps:CityState>
			<ymaps:Zip>
				<xsl:value-of select=".//yahoo:Address//yahoo:PostalCode"/>
			</ymaps:Zip>
		       <!-- Assume all locations are in the US -->
			<ymaps:Country>US</ymaps:Country>
		</item>
	</xsl:template>
</xsl:stylesheet>
