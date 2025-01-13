<?php



$opts = array('http' => array('proxy'=> 'tcp://www-cache:3128', 'request_fulluri'=> true), 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false));
$context = stream_context_create($opts);

// Obtenir l'adresse IP du client
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $clientIP = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $clientIP = $_SERVER['REMOTE_ADDR'];
}


// API de géolocalisation
$geoApiUrl = "http://ip-api.com/json/{$clientIP}";
$geoData = file_get_contents($geoApiUrl, false, $context); 
$geoData = json_decode($geoData, true);

// Si la géolocalisation n'est pas Nancy, utiliser les coordonnées de l'IUT Charlemagne
if ($geoData['city'] !== 'Nancy') {
    $geoData['lat'] = 48.6937; // Latitude de l'IUT Charlemagne
    $geoData['lon'] = 6.1834;  // Longitude de l'IUT Charlemagne
}

// API de météorologie (OpenWeatherMap ou similaire)
$weatherApiUrl = "https://www.infoclimat.fr/public-api/gfs/xml?_ll=48.67103,6.15083&_auth=ARsDFFIsBCZRfFtsD3lSe1Q8ADUPeVRzBHgFZgtuAH1UMQNgUTNcPlU5VClSfVZkUn8AYVxmVW0Eb1I2WylSLgFgA25SNwRuUT1bPw83UnlUeAB9DzFUcwR4BWMLYwBhVCkDb1EzXCBVOFQoUmNWZlJnAH9cfFVsBGRSPVs1UjEBZwNkUjIEYVE6WyYPIFJjVGUAZg9mVD4EbwVhCzMAMFQzA2JRMlw5VThUKFJiVmtSZQBpXGtVbwRlUjVbKVIuARsDFFIsBCZRfFtsD3lSe1QyAD4PZA%3D%3D&_c=19f3aa7d766b6ba91191c8be71dd1ab2";

// Étape 1 : Télécharger l'XML depuis l'API
$response = file_get_contents($weatherApiUrl);

// Vérifier si la réponse a été téléchargée avec succès
if ($response === false) {
    die("Erreur : Impossible de télécharger les données XML depuis l'API.");
}

// Étape 2 : Ajouter une déclaration DOCTYPE pour inclure la DTD
$dtdPath = 'weather.dtd'; // Chemin relatif ou absolu du fichier DTD
$xmlWithDtd = '<!DOCTYPE previsions SYSTEM "' . $dtdPath . '">' . $response;

// Étape 3 : Charger l'XML modifié dans DOMDocument
$domXml = new DOMDocument();
$domXml->validateOnParse = true; // Activer la validation lors du chargement

// Vérifier si l'XML a été chargé correctement
if (!$domXml->loadXML($xmlWithDtd)) {
    die("Erreur : Impossible de charger l'XML après modification.");
}

// Étape 4 : Valider l'XML contre le DTD
if ($domXml->validate()) {
    echo "Succès : L'XML est valide par rapport au DTD.\n";
} else {
    die("Erreur : L'XML n'est pas valide par rapport au DTD.");
}

// Étape 5 : Charger la feuille de style XSLT
$xslPath = 'weather.xsl'; // Chemin vers le fichier XSLT
$xsl = new DOMDocument();

if (!$xsl->load($xslPath)) {
    die("Erreur : Impossible de charger le fichier XSLT.");
}

// Étape 6 : Transformer l'XML en HTML avec XSLTProcessor
$proc = new XSLTProcessor();
$proc->importStylesheet($xsl);

$htmlFragment = $proc->transformToXML($domXml);

// Vérifier si la transformation a réussi
if ($htmlFragment === false) {
    die("Erreur : Impossible de transformer l'XML en HTML.");
}






$wazeApiUrl = "https://carto.g-ny.org/data/cifs/cifs_waze_v2.json";
$wazeData = file_get_contents($wazeApiUrl, false, $context);
$wazeData = json_decode($wazeData, true);

$trafficIncidents = [];
if (isset($wazeData['alerts'])) {
    foreach ($wazeData['alerts'] as $alert) {
        
        $coordinates = explode(' ', $alert['location']['polyline']);
        $lat = $coordinates[0];
        $lon = $coordinates[1];

        $trafficIncidents[] = [
            'lat' => $lat,
            'lon' => $lon,
            'type' => $alert['type'],
            'description' => $alert['description'],
            'street' => $alert['location']['street'],
            'starttime' => $alert['starttime'],
            'endtime' => $alert['endtime'],
            'source' => $alert['source']['name']
        ];
    }
};




// Obtenir les données de qualité de l'air 


$airQualityApiUrl = "https://services3.arcgis.com/Is0UwT37raQYl9Jj/arcgis/rest/services/ind_grandest/FeatureServer/0/query?where=lib_zone%3D%27Nancy%27&orderByFields=date_ech%20DESC&outFields=*&resultRecordCount=1&f=pjson";
$airQualityData = file_get_contents($airQualityApiUrl, false, $context); 
$airQualityData = json_decode($airQualityData, true);

// Extraire l'indice de qualité de l'air (s'il est disponible)
$airQualityIndex = $airQualityData['features'][0]['attributes']['code_qual'] ?? 'Non disponible';
$airQualityLabel = $airQualityData['features'][0]['attributes']['lib_qual'] ?? 'Non disponible';
$airQualityColor = $airQualityData['features'][0]['attributes']['coul_qual'] ?? '';

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

    <h1>Informations sur le Trafic et la Météo</h1>
  
    <div id='map' style='height: 400px; margin-top: 20px;'></div>
    <div id='air-quality' style='background-color:{$airQualityColor}'>
        <h2>Qualité de l'Air</h2>
        <p > Index: {$airQualityIndex} - {$airQualityLabel}</p>
    </div>
    <footer>
        <p>API utilisées :</p>
        <ul>
            <li><a href='{$geoApiUrl}'>Géolocalisation IP</a></li>
            <li><a href='{$airQualityApiUrl}'>Qualité de l'Air</a></li>
            <li><a href='{$wazeApiUrl}'>Trafic Waze</a></li>
        </ul>
    </footer>
    <script>
        // Configuration de la carte Leaflet
        var map = L.map('map').setView([{$geoData['lat']}, {$geoData['lon']}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);
        L.marker([{$geoData['lat']}, {$geoData['lon']}]).addTo(map)
            .bindPopup('Emplacement du Client')
            .openPopup();

        
        var trafficIncidents = " . json_encode($trafficIncidents) . ";
        trafficIncidents.forEach(function(incident) {
            L.marker([incident.lat, incident.lon]).addTo(map)
                .bindPopup(
                    '<b>' + incident.type + '</b><br>' +
                    'Description: ' + incident.description + '<br>' +
                    'Rue: ' + incident.street + '<br>' +
                    'Début: ' + incident.starttime + '<br>' +
                    'Fin: ' + incident.endtime + '<br>' +
                    'Source: ' + incident.source
                );
        });
    </script>
</body>
</html>";



// Afficher le HTML généré
echo $html;
?>