/*----------------REQUETE AJAX-------------------*/
var points = {};
var xmlhttp = new XMLHttpRequest();
xmlhttp.open("GET", "../../wp-admin/admin-ajax.php?action=get_implantations", false); //Version distante
//xmlhttp.open("GET", "../wp-admin/admin-ajax.php?action=get_implantations", false); //Version local
xmlhttp.onreadystatechange = function () {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        response = JSON.parse(xmlhttp.responseText);
        plugin_url = response.plugin_url;
        points = response.points;
        
    }

};
xmlhttp.send(null);

/*--------------MAP-----------------*/

/*Variable pour infobulle*/
var infowindow;

function initialize() {
    /*Création de la map*/
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: new google.maps.LatLng(14.6500000, -61.0297823),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 11
    });

    /*Parcour du JSON*/
    for (var i in points) {
        var implant = points[i];
        var location = new google.maps.LatLng(implant.lat, implant.lng);
        var description = implant["description"]; /*La description correspondante au statut de l'implanation*/
        addMarker(map, implant.name, implant.description, location, implant.statut, implant.date_parution, description);
    }
}


/*--Fonction pour ajouter le marker--*/
function addMarker(map, name, description, location, statut, date_parution, message) {

    /*Creation du pin vert - orange - rouge */
    var icon = plugin_url + "/front/assets/" + statut + ".png"; /*à mettre en relatif si possible*/

    /*icon*/
    var marker = new google.maps.Marker({
        position: location,
        map: map,
        title: name,
        icon: new google.maps.MarkerImage(icon)
    });


    /*----Infos bulle-----*/

    /*Création du contenu*/
    var date = (statut !== 'fonctionnement') ? '<strong class="cc-date">' + date_parution+ '</strong>' : '',
        signalerButton = (statut == "fonctionnement") ? '<a href="/contact">Signaler une panne</a>' : '', /*On affiche le btn seulement si l'implantation fonctionne*/
        contentString = '<div class="cc-info-bulle"><h5>' + name + '</h5>' + date + '<p>' +  message + '</p>' + signalerButton + '</div>';

    /*Info bulle*/
    google.maps.event.addListener(marker, 'click', function () {
        /*On ferme les infos window ouvertes*/
        if (typeof infowindow != 'undefined')
            infowindow.close();
        infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        infowindow.open(map, marker);
    });
}

google.maps.event.addDomListener(window, 'load', initialize);
