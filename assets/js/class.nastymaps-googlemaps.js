class NastyMaps_GoogleMaps {
    constructor(selector, apiKey, options = {}, locations = []) {
        this.apiKey = apiKey;
        this.mapElement = document.querySelector(selector);
        if (!this.mapElement) {
            throw new Error(`Element with selector "${selector}" not found.`);
        }

        this.mapOptions = options;
        this.locations = locations;

        this.loadGoogleMapsAPI().then(() => {
            this.initMap();
        }).catch(error => {
            throw new Error("Google Maps API failed to load: " + error.message);
        });
    }

    loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined' && google.maps) {
                resolve();
            } else {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${this.apiKey}`;
                script.async = true;
                script.defer = true;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            }
        });
    }

    setCenter(lat, lng) {
        this.map.setCenter({ lat, lng });

        return this;
    }

    setZoom(zoomLevel) {
        this.map.setZoom(zoomLevel);

        return this;
    }

    setOptions(options) {
        this.mapOptions = options;
        this.map.setOptions(options);

        return this;
    }

    setLocations(locations) {
        this.locations = locations;

        return this;
    }

    addLocationMarkers() {
        for (let location of this.locations) {
            this.addMarker(location.lat, location.lng, location.popupText, location.icon);
        }

        return this;
    }

    addMarker(lat, lng, popupText = '', iconObj = {}) {
        let markerOptions = {
            position: { lat, lng },
            map: this.map
        };

        if (iconObj.iconUrl) {
            markerOptions.icon = {
                url: iconObj.iconUrl,
                size: new google.maps.Size(iconObj.iconSize[0], iconObj.iconSize[1]),
                anchor: new google.maps.Point(iconObj.iconAnchor[0], iconObj.iconAnchor[1])
            };
        }

        let marker = new google.maps.Marker(markerOptions);
        if (popupText) {
            let infoWindow = new google.maps.InfoWindow({
                content: popupText
            });
            marker.addListener('click', () => {
                infoWindow.open(this.map, marker);
            });
        }

        return this;
    }

    initMap() {
        this.map = new google.maps.Map(this.mapElement, {
            center: this.mapOptions.center || { lat: 49.568039, lng: 10.8793036 },
            zoom: this.mapOptions.zoom || 8,
            scrollwheel: false
        });

        if (this.locations.length) {
            this.addLocationMarkers();
        }

        return this;
    }
}
