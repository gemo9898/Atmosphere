<?xml version="1.0" encoding="UTF-8"?>
<!-- Feuille de style XSL pour transformer les données météorologiques en HTML -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <!-- Modèle pour correspondre à l'élément racine "weather" -->
    <xsl:template match="/weather">
        <div class="weather-info">
            <!-- Titre de la section météo -->
            <h2>Informations Météorologiques</h2>
            <!-- Affichage de la température -->
            <p>Température: <xsl:value-of select="temperature"/> °C</p>
            <!-- Affichage de la description météo -->
            <p>Description: <xsl:value-of select="description"/></p>
        </div>
    </xsl:template>
</xsl:stylesheet>