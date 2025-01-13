<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<!DOCTYPE previsions SYSTEM "weather.dtd">
  <!-- Définir le modèle principal -->
  <xsl:template match="/">
    <html>
      <head>
        <title>Prévisions Météo</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .section { margin-bottom: 20px; }
          .symbol { font-size: 1.5em; margin-right: 10px; }
        </style>
      </head>
      <body>
        <h1>Prévisions Météo</h1>

        <xsl:apply-templates select="previsions/echeance"/>
      </body>
    </html>
  </xsl:template>

  <!-- Traiter chaque &lt;echeance&gt; -->
  <xsl:template match="echeance">
    <div class="section">
      <h2>Prévisions pour <xsl:value-of select="@timestamp"/></h2>

      <!-- Température -->
      <p>
        <span class="symbol">🌡️</span>
        Température : <xsl:value-of select="temperature/level[@val='2m']"/> K
        <xsl:if test="number(temperature/level[@val='2m']) &lt; 273.15"> (Froid)</xsl:if>
      </p>

      <!-- Pluie -->
      <p>
        <span class="symbol">🌧️</span>
        Précipitations : <xsl:value-of select="pluie[@interval='3h']"/> mm
        <xsl:if test="pluie[@interval='3h'] &gt; 0"> (Pluie possible)</xsl:if>
      </p>

      <!-- Neige -->
      <p>
        <span class="symbol">❄️</span>
        Risque de neige : <xsl:value-of select="risque_neige"/>
      </p>

      <!-- Vent -->
      <p>
        <span class="symbol">💨</span>
        Vent moyen : <xsl:value-of select="vent_moyen/level[@val='10m']"/> km/h,
        Rafales : <xsl:value-of select="vent_rafales/level[@val='10m']"/> km/h
      </p>
    </div>
  </xsl:template>

</xsl:stylesheet>
