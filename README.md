# Liens importants

## Repo git
- https://github.com/gemo9898/Atmosphere
  
## Webetu
- https://webetu.iutnc.univ-lorraine.fr/~e18821u/Interop/Atmosphere/

# Description du Code

## 1. Configuration du Contexte de Stream
- Un contexte de stream a été configuré pour gérer les requêtes HTTP et SSL, incluant un proxy et désactivant la vérification des certificats SSL.

## 2. Obtention de l'IP du Client
- L'adresse IP du client est obtenue en utilisant `$_SERVER['HTTP_CLIENT_IP']`, `$_SERVER['HTTP_X_FORWARDED_FOR']`, ou `$_SERVER['REMOTE_ADDR']`.

## 3. Géolocalisation
- L'API de `ip-api.com` est utilisée pour obtenir la géolocalisation basée sur l'IP du client.
- Si la ville n'est pas "Nancy", les coordonnées de l'IUT Charlemagne sont utilisées.

## 4. Obtention des Données Météorologiques
- Une requête est effectuée à l'API de `infoclimat.fr` pour obtenir des données météorologiques au format XML.
- Une déclaration DOCTYPE est ajoutée pour inclure une DTD, et le XML est validé par rapport à cette DTD.
- Une feuille de style XSLT est chargée pour transformer le XML en HTML.

## 5. Obtention des Données de Trafic
- Une requête est effectuée à l'API de Waze pour obtenir des données sur les incidents de trafic.
- Les données sont traitées et stockées dans un tableau pour une utilisation ultérieure dans la génération de la carte.

## 6. Obtention des Données de Qualité de l'Air
- Une requête est effectuée à une API d'ArcGIS pour obtenir des données sur la qualité de l'air à Nancy.
- L'indice de qualité de l'air, son étiquette et la couleur associée sont extraits.

## 7. Génération du HTML Final
- Une page HTML est générée, incluant :
  - Les informations météorologiques transformées depuis le XML.
  - Une carte interactive avec Leaflet montrant l'emplacement du client et les incidents de trafic.
  - Les informations sur la qualité de l'air.
  - Un pied de page avec des liens vers les APIs utilisées.

---

# Erreur 504

## Description de l'Erreur
- L'erreur **504 Gateway Timeout** se produit lorsqu'un serveur ne reçoit pas de réponse en temps opportun d'un autre serveur agissant comme une passerelle ou un proxy. Dans ce cas, il est probable que l'une des APIs externes (comme celle de Waze, Infoclimat ou ArcGIS) n'ait pas répondu à temps.

## Conséquences
- En raison de l'erreur 504, les données de l'application ne s'affichent pas correctement. Cela peut affecter l'affichage de :
  - Données météorologiques.
  - Incidents de trafic.
  - Qualité de l'air.

## Solutions Possibles
- **Réessayer la Requête :** Implémenter un mécanisme de réessai pour les requêtes échouées.
- **Gestion des Erreurs :** Ajouter une gestion des erreurs plus robuste pour afficher un message convivial à l'utilisateur en cas d'échec.
- **Optimisation des Temps d'Attente :** Ajuster les délais d'attente des requêtes HTTP pour éviter les timeouts.
- **Utilisation du Cache :** Implémenter un système de cache pour stocker temporairement les données des APIs et éviter des requêtes répétées en cas d'échec.

Cette erreur est courante dans les applications dépendant de services externes et peut être atténuée avec les stratégies mentionnées.
