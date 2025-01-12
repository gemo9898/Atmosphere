<?php
// Configuration du proxy
$opts = array(
    'http' => array(
        'proxy' => 'tcp://www-cache:3128',
        'request_fulluri' => true
    ),
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false
    )
);
$context = stream_context_create($opts);

// Obtenir l'adresse IP du client
$clientIP = $_SERVER['REMOTE_ADDR'];

// API de géolocalisation
$geoApiUrl = "http://ip-api.com/json/{$clientIP}";
$geoData = file_get_contents($geoApiUrl, false, $context);
$geoData = json_decode($geoData, true);

// Si la géolocalisation n'est pas Nancy, utiliser les coordonnées de l'IUT Charlemagne
if ($geoData['city'] !== 'Nancy') {
    $geoData['lat'] = 48.6937; // Latitude de l'IUT Charlemagne
    $geoData['lon'] = 6.1834;  // Longitude de l'IUT Charlemagne
}

// API de météorologie (utilisant OpenWeatherMap comme exemple)
$weatherApiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$geoData['lat']}&lon={$geoData['lon']}&appid=TU_API_KEY&units=metric&lang=es";
$weatherData = file_get_contents($weatherApiUrl, false, $context);
$weatherData = json_decode($weatherData, true);

// Charger le fichier XSL
$xsl = new DOMDocument();
$xsl->load('weather.xsl');

// Créer le processeur XSLT
$proc = new XSLTProcessor();
$proc->importStylesheet($xsl);

// Créer le document XML avec les données météorologiques
$xml = new DOMDocument();
$xml->loadXML('<weather><temperature>' . $weatherData['main']['temp'] . '</temperature><description>' . $weatherData['weather'][0]['description'] . '</description></weather>');

// Transformer XML en HTML
$htmlFragment = $proc->transformToXML($xml);

// Obtenir les données de qualité de l'air (utilisant la nouvelle API)
$airQualityApiUrl = "https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&orderByFields=date_ech%20DESC&outFields=*&resultRecordCount=1&f=pjson";
$airQualityData = file_get_contents($airQualityApiUrl, false, $context);
$airQualityData = json_decode($airQualityData, true);

// Extraire l'indice de qualité de l'air (s'il est disponible)
$airQualityIndex = $airQualityData['features'][0]['attributes']['indice'] ?? 'Non disponible';

// Générer le HTML final
$html = "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <title>Informations sur le Trafic et la Météo</title>
    <link rel='stylesheet' href='style.css'>
    <link rel='stylesheet' href='https://unpkg.com/leaflet/dist/leaflet.css' />
    <script src='https://unpkg.com/leaflet/dist/leaflet.js'></script>
</head>
<body>
    <h1>Informations sur le Trafic et la Météo</h1>
    <div id='weather'>
        {$htmlFragment}
    </div>
    <div id='map' style='height: 400px; margin-top: 20px;'></div>
    <div id='air-quality'>
        <h2>Qualité de l'Air</h2>
        <p>Indice de Qualité de l'Air (PM2.5): {$airQualityIndex}</p>
    </div>
    <footer>
        <p>API utilisées :</p>
        <ul>
            <li><a href='{$geoApiUrl }'>Géolocalisation IP</a></li>
            <li><a href='{$weatherApiUrl}'>Données Météorologiques</a></li>
            <li><a href='{$airQualityApiUrl}'>Qualité de l'Air</a></li>
        </ul>
    </footer>
    <script>
        // Configuration de la carte Leaf let
        var map = L.map('map').setView([{$geoData['lat']}, {$geoData['lon']}], 13);
        L.tileLayer ('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
        L.marker([{$geoData['lat']}, {$geoData['lon']}]).addTo(map)
            .bindPopup('Emplacement du Client')
            .openPopup();
    </script>
</body>
</html>";

// Enregistrer le HTML généré
file_put_contents('generated.html', $html);

// Afficher le HTML généré
echo $html;
?>