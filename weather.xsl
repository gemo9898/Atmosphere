<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/">
    <html>
      <head>
        <title>Prévisions météorologiques</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; }
          h1 { color: #2E8B57; }
          .climate-info { margin-top: 20px; }
          .condition { font-weight: bold; }
          .symbol { font-size: 24px; }
        </style>
      </head>
      <body>
        <h1>Prévisions météorologiques</h1>
        <div class="climate-info">
          <h2>Conditions pour demain</h2>

          <!-- Température -->
          <p><span class="condition">Température :</span> 
            <xsl:value-of select="temperature/level[@val='2m']"/>°C
          </p>

          <!-- Vent moyen -->
          <p><span class="condition">Vent :</span> 
            <xsl:value-of select="vent_moyen/level[@val='10m']"/> km/h 
            <xsl:if test="vent_moyen/level[@val='10m']"> <span class="symbol">🌬️</span> </xsl:if>
          </p>

          <!-- Pluie -->
          <p><span class="condition">Pluie (3 heures) :</span> 
            <xsl:value-of select="pluie"/> mm 
            <xsl:if test="pluie &gt; 0">
              <span class="symbol">🌧️</span>
            </xsl:if>
          </p>

          <!-- Risque de neige -->
          <p><span class="condition">Risque de neige :</span> 
            <xsl:value-of select="risque_neige"/>
            <xsl:if test="risque_neige = 'oui'">
              <span class="symbol">❄️</span>
            </xsl: if>
          </p>
          
          <!-- Humidité -->
          <p><span class="condition">Humidité :</span> 
            <xsl:value-of select="humidite/level[@val='2m']"/> %
          </p>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>