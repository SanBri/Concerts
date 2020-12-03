class EventsRequest {

    request() {
        let zoom
        let queryConcerts = document.getElementById('queryConcerts').value
        var request = new XMLHttpRequest()
        request.onreadystatechange = () => {
            console.log(request.readyState)
            if (request.readyState == XMLHttpRequest.DONE && request.status == 200) {
                var response = request.responseText
                let resultats = JSON.parse(response);
                if ( resultats.length > 1) {
                    zoom = 12
                } else {
                    zoom = 18
                }
                map = new GoogleMap('map', resultats[0].latitude, resultats[0].longitude, zoom)
                map.addMarkers(resultats)
            }
        }
        request.open("GET", "https://127.0.0.1:8000/localisations_concerts/" + queryConcerts)
        request.send(null)
    }
    
}
