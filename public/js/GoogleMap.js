class GoogleMap {

    constructor(mapId, lat, lng, zoom) {
        this.mapId = mapId
        this.lat = lat
        this.lng = lng
        this.zoom = zoom
        this.map = new google.maps.Map(document.getElementById(this.mapId), {  center : {lat: this.lat, lng: this.lng},
            zoom: this.zoom
        })
    }

    addMarker() {
        let marker = new google.maps.Marker({
            position: {lat: this.lat, lng: this.lng},
            map: this.map
        })
    }
    
    addMarkers(concerts) {
        concerts.forEach(concert => {
            let marker = new google.maps.Marker({
                position: {lat: concert.latitude, lng: concert.longitude},
                map: this.map,
            })
            // On attribue à chaque marqueur un ID pour le relier à la div de l'événement correspondant :
            marker.set("type", "point");
            marker.set("id", concert.id);
            let markerId = marker.get("id")
            // Création de l'infobulle :
            let infowindow = new google.maps.InfoWindow({
                content: concert.name,
                size: new google.maps.Size(100, 100)
            });
            google.maps.event.addListener(marker, 'mouseover', () => {
                infowindow.open(map,marker) // Apparition de l'infobulle au survol du marqueur
                this.show(markerId) // Mise en valeur de la div d'événement correspondant au marqueur
            })
            google.maps.event.addListener(marker, 'mouseout', () => {
                infowindow.close(map,marker) // Disparition de l'infobulle quand la souris ne survole plus le marqueur
                this.hide(markerId) // La div d'événement correspondant au marqueur n'est plus mise en valeur
            })
            google.maps.event.addListener(marker, 'click', () => {
                let url = document.location.href
                document.location.href= url + 'concert/' + markerId
            })
            // Centrage sur le marqueur correspondant à la div d'événement :
            document.getElementById(markerId).addEventListener('mouseenter', () => {
                this.map.setCenter({lat: concert.latitude, lng: concert.longitude})
                infowindow.open(map,marker)
                this.show(markerId)
            })
            document.getElementById(markerId).addEventListener('mouseleave', () => {
                infowindow.close(map,marker)
                this.hide(markerId)
            })
        })
    }

    show(toShow) {
        let blocks = document.querySelectorAll('.each-block')
        blocks.forEach(block => {
            block.style.transition = '0.45s' 
            block.style.backgroundColor = 'rgba(104, 93, 104, 0.1)'
            block.style.border = 'none'
        })
        let activeBlock = document.getElementById(toShow)
        activeBlock.style.transition = '0.45s' 
        activeBlock.style.backgroundColor = 'rgba(104, 93, 104, 0.3)'
        activeBlock.style.filter = 'drop-shadow(0px 10px 5px rgba(206, 182, 206, 0.7));'
    }

    hide(toHide) {
        let activeBlock = document.getElementById(toHide)
        activeBlock.style.transition = '0.45s' 
        activeBlock.style.backgroundColor = 'rgba(104, 93, 104, 0.1)'
        activeBlock.style.border = 'none'
    }
}

map = document.getElementById('map')

if (document.getElementById('map') ) {
    lat = parseFloat(map.dataset.lat)
    lng = parseFloat(map.dataset.lng)
    function initMap() {
        if (lat && lng) {
            map = new GoogleMap('map', lat, lng, 15)
            map.addMarker();
        } else {
            query = new EventsRequest()
            let concerts = query.request()
        }

    } 
}


