class NastyMaps_OpenStreetMap {
    constructor(selector, options = {}, locations = []) {
        this.leaflet_present = typeof L !== 'undefined';
        if (!this.leaflet_present) {
            throw new Error("Leaflet library not found. Make sure you have included the Leaflet library in your project.");
        }

        this.mapElement = document.querySelector(selector);
        if (!this.mapElement) {
            throw new Error(`Element with selector "${selector}" not found.`);
        }

        this.mapOptions = options;
        this.locations = locations;

        return this.initMap();
    }

    setCenter(lat, lng) {
        this.map.setView([lat, lng], this.map.getZoom());
        
        return this;
    }

    setZoom(zoomLevel) {
        this.map.setZoom(zoomLevel);

        return this;
    }

    setOptions(options) {
        this.mapOptions = options;

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
        let markerOptions = {};
        if (iconObj.iconUrl) {
            let customIcon = L.icon({
                iconUrl: iconObj.iconUrl,
                iconSize: iconObj.iconSize || [32, 32],
                iconAnchor: iconObj.iconAnchor || [16, 32],
                popupAnchor: iconObj.popupAnchor || [0, -32]
            });
            markerOptions.icon = customIcon;
        }
        
        let marker = L.marker([lat, lng], markerOptions).addTo(this.map);
        if (popupText) {
            marker.bindPopup(popupText);
        }

        return this;
    }

    initMap() {
        this.map = L.map(this.mapElement, {
            center: options.center || [49.568039, 10.8793036],
            zoom: options.zoom || 8,
            scollWheelZoom: false
        });
        
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: "&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap contributors</a>"
        }).addTo(this.map);

        L.control.scale().addTo(this.map);

        if (Object.keys(this.locations).length) {
            this.addLocationMarkers();
        }

        return this;
    }
}
