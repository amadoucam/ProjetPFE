// On initialise la latitude et la longitude de Jendouba (centre de la carte)
var lat = 36.5012361; 
var lon = 8.7772554;

var macarte = null;
// Fonction d'initialisation de la carte
function initMap() {
    var markers = []; // Nous initialisons la liste des marqueurs
    // Nous définissons le dossier qui contiendra les marqueurs
    var iconBase = 'http://localhost/carte/icons/';
    // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
    macarte = L.map('map').setView([lat, lon], 11);
    markerClusters = L.markerClusterGroup(); // Nous initialisons les groupes de marqueurs
    // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        // Il est toujours bien de laisser le lien vers la source des données
        attribution: 'données © OpenStreetMap/ODbL - rendu OSM France',
        minZoom: 1,
        maxZoom: 20
    }).addTo(macarte);
    // Nous parcourons la liste des villes
    for (ville in villes) {
        // Nous définissons l'icône à utiliser pour le marqueur, sa taille affichée (iconSize), sa position (iconAnchor) et le décalage de son ancrage (popupAnchor)
        var myIcon = L.icon({
            iconUrl: iconBase + "autres.png",
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [-3, -76],
        });
        var marker = L.marker([villes[ville].lat, villes[ville].lon], { icon: myIcon }); // pas de addTo(macarte), l'affichage sera géré par la bibliothèque des clusters
        marker.bindPopup(ville);
        markerClusters.addLayer(marker); // Nous ajoutons le marqueur aux groupes
        markers.push(marker); // Nous ajoutons le marqueur à la liste des marqueurs
    }
    var group = new L.featureGroup(markers); // Nous créons le groupe des marqueurs pour adapter le zoom
    macarte.fitBounds(group.getBounds().pad(0.5)); // Nous demandons à ce que tous les marqueurs soient visibles, et ajoutons un padding (pad(0.5)) pour que les marqueurs ne soient pas coupés
    macarte.addLayer(markerClusters);
}
window.onload = function(){
// Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
initMap(); 
};