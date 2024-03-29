@include('includes/map-icons')
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
<script>
    var center, orders, latitude, longitude, bounds;
    var clicked_markers = new Array;
    var markers = new Array;

    function setMapsArguments(orders, key, center = null, travelMode = null, cluster = true, markerAdd = false) {
        this.center = center;
        this.orders = orders;
        this.key = key;
        this.travelMode = travelMode;
        this.cluster = cluster;
        this.markerAdd = markerAdd;
    }

    function loadScript() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success, error);
        } else {
            error();
        }
    }

    function success(pos) {
        var crd = pos.coords;
        latitude = crd.latitude;
        longitude = crd.longitude;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + key + '&callback=initialize';
        script.async = true;
        document.body.appendChild(script);
    }

    function error(err) {
        latitude = center.lat;
        longitude = center.lng;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://maps.googleapis.com/maps/api/js?key=' + key + '&callback=initialize';
        script.async = true;
        document.body.appendChild(script);
    }


    function initialize() {
        var markers = [];
        var directionsService = new google.maps.DirectionsService;
        var directionsRenderer = new google.maps.DirectionsRenderer(
            {
                suppressMarkers: true
            }
        );

        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [
                {
                    featureType: "poi",
                    stylers: [{visibility: "off"}],
                },
                {
                    featureType: "transit",
                    elementType: "labels.icon",
                    stylers: [{visibility: "off"}],
                },
            ]
        };

        var LatLngList = new Array;

        if (center != null) {
            var gData = new google.maps.LatLng(center['lat'], center['lng']);
            LatLngList.push(gData);
            if (latitude != center['lat']) {
                var gData = new google.maps.LatLng(latitude, longitude);
            }
            LatLngList.push(gData);
        }

        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var gData = new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']);
            LatLngList.push(gData);
        }

        map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

        if (travelMode != null) {
            directionsRenderer.setMap(map);
            var waypts = [];
            for (var i = 1; i < LatLngList.length; i++) {
                waypts.push({
                    location: LatLngList[i],
                    stopover: true
                });
            }
            directionsService.route({
                origin: LatLngList[1],
                destination: LatLngList[0],
                waypoints: waypts,
                optimizeWaypoints: true,
                travelMode: travelMode
            }, function (response, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(response);
                    // console.log(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
        ;

        var infowindow = new google.maps.InfoWindow();
        var infoWindowClosed = true;

        function bindInfoWindow(marker, map, html) {
            marker.addListener('click', () => {
                if (markerAdd) {
                    MarkerClick(marker);
                } else {
                    if (infoWindowClosed) {
                        infowindow.setContent(html);
                        infowindow.open(map, marker);
                        infoWindowClosed = false;
                    } else {
                        infowindow.close(map, marker);
                        infoWindowClosed = true;
                    }
                }
            });

        }

        if (center != null) {
            var html = "<p><b>" + center['name'] + "</b> <br/>" + center['street'];
            var marker = new mapIcons.Marker({
                position: new google.maps.LatLng(center['lat'], center['lng']),
                map: map,
                html: html,
                icon: {
                    path: mapIcons.shapes.MAP_PIN,
                    fillColor: '#00CCBB',
                    fillOpacity: 1,
                    strokeColor: '',
                    strokeWeight: 0
                },
                id: 0,
                map_icon_label: '<span class="map-icon map-icon-local-government"></span>'

            });
            markers.push(marker), bindInfoWindow(marker, map, html);


            if (latitude != center['lat']) {
                var html = "<p><b>Deine Position</b>";
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(latitude, longitude),
                    map: map,
                    html: html,
                    id: 0,
                });
                markers.push(marker), bindInfoWindow(marker, map, html);
            }
        }

        var icon_url = (order) => ((travelMode != null) && (orders[i].order_status_id > 15)) || ((travelMode == null) && (order.route_id == null)) ? "https://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_green.png" : "https://maps.gstatic.com/mapfiles/markers2/marker.png";

        for (var i = 0, order_len = orders.length; i < order_len; i++) {
            var html = "<p><b>" + orders[i].address['firstname'] + " " + orders[i].address['name'] + "</b> <br/>" + orders[i].address['street'] + "<br/> Zopf: " + orders[i]['quantity'];
            var marker = new mapIcons.Marker({
                position: new google.maps.LatLng(orders[i].address['lat'], orders[i].address['lng']),
                map: map,
                html: html,
                icon: new google.maps.MarkerImage(icon_url(orders[i])),
                id: orders[i].id,
            });
            markers.push(marker), bindInfoWindow(marker, map, html);
        }

        if (cluster == true) {
            var markerClusteres = null;
            markerClusteres = new markerClusterer.MarkerClusterer({map, markers, onClusterClick,});

            function onClusterClick(event, cluster, map) {
                var content = '';
                var info = new google.maps.MVCObject;
                var clickedMarkers = cluster.markers;
                for (var i = 0; i < clickedMarkers.length; i++) {
                    var html = clickedMarkers[i].html;
                    content += html;
                }

                infowindow.setContent(content);
                console.log(event);
                console.log(cluster);
                var latLng = event.latLng.toJSON();
                info.set('position', new google.maps.LatLng(cluster.bounds.Va['hi'], latLng['lng']));
                // console.log(info.position.toJson());
                // info.position.lat -= 0.05
                infowindow.open(map, info);

            };
            // , {
            //     imagePath: 'https://cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m', zoomOnClick: false
            // });
            // markerClusteres.addListener('clusterclick', function (cluster) {
            //     var content = '';
            //     var info = new google.maps.MVCObject;
            //     info.set('position', cluster.center_);
            //     var clickedMarkers = cluster.getMarkers();
            //     for (var i = 0; i < clickedMarkers.length; i++) {
            //         var html = clickedMarkers[i].html;
            //         content += html;
            //     }
            //     infowindow.setContent(content);
            //     infowindow.open(map, info);
            // });
        }

        bounds = new google.maps.LatLngBounds();
        for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
            bounds.extend(LatLngList[i]);
        }

        map.fitBounds(bounds);

    }


    function MapResize() {
        map.fitBounds(bounds);
    }

</script>
