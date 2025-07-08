class GeocodingMaps {
    constructor() {
        this.map = null;
        this.marker = null;
        this.geocodingService = null;
        this.apiKey = null;
        this.initializeApiKey();
    }

    initializeApiKey() {
        const apiKeyMeta = document.querySelector('meta[name="geoapify-api-key"]');
        if (apiKeyMeta) {
            this.apiKey = apiKeyMeta.getAttribute('content');
        }
    }

    async geocodeAddress(address) {
        if (!this.apiKey) {
            console.error('Geoapify API key not configured');
            return null;
        }

        try {
            const response = await fetch(`https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(address)}&apiKey=${this.apiKey}&limit=1`);
            const data = await response.json();

            if (data.features && data.features.length > 0) {
                const feature = data.features[0];
                const properties = feature.properties;
                const coordinates = feature.geometry.coordinates;

                return {
                    latitude: coordinates[1],
                    longitude: coordinates[0],
                    formatted_address: properties.formatted || address,
                    valid: true
                };
            }
            return null;
        } catch (error) {
            console.error('Geocoding error:', error);
            return null;
        }
    }

    async reverseGeocode(latitude, longitude) {
        if (!this.apiKey) {
            console.error('Geoapify API key not configured');
            return null;
        }

        try {
            const response = await fetch(`https://api.geoapify.com/v1/geocode/reverse?lat=${latitude}&lon=${longitude}&apiKey=${this.apiKey}&limit=1`);
            const data = await response.json();

            if (data.features && data.features.length > 0) {
                const feature = data.features[0];
                const properties = feature.properties;

                return {
                    formatted_address: properties.formatted || null,
                    valid: true
                };
            }
            return null;
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            return null;
        }
    }

    initializeMap(containerId, latitude = 50, longitude = 15.2551, zoom = 3) {
        if (!this.apiKey) {
            console.error('Geoapify API key not configured');
            return null;
        }

        this.map = L.map(containerId).setView([latitude, longitude], zoom);

        L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${this.apiKey}`, {
            attribution: '© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 20
        }).addTo(this.map);

        if (latitude !== 54.5260 && longitude !== 15.2551) {
            this.addMarker(latitude, longitude);
        }

        return this.map;
    }

    addMarker(latitude, longitude, preserveView = false) {
        if (this.marker) {
            this.map.removeLayer(this.marker);
        }

        this.marker = L.marker([latitude, longitude]).addTo(this.map);

        if (!preserveView) {
            this.map.setView([latitude, longitude], 13);
        }
    }

    setupAddressGeocoding(addressInputId, latitudeInputId, longitudeInputId, mapContainerId) {
        const addressInput = document.getElementById(addressInputId);
        const latitudeInput = document.getElementById(latitudeInputId);
        const longitudeInput = document.getElementById(longitudeInputId);
        const mapContainer = document.getElementById(mapContainerId);

        if (!addressInput || !latitudeInput || !longitudeInput || !mapContainer) {
            console.error('Required elements not found for address geocoding setup');
            return;
        }

        if (latitudeInput.value && longitudeInput.value) {
            this.initializeMap(mapContainerId, parseFloat(latitudeInput.value), parseFloat(longitudeInput.value), 13);
        } else {
            this.initializeMap(mapContainerId);
        }

        this.map.on('click', async (e) => {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            latitudeInput.value = lat;
            longitudeInput.value = lng;

            this.addMarker(lat, lng, true);

            const result = await this.reverseGeocode(lat, lng);
            if (result && result.formatted_address) {
                addressInput.value = result.formatted_address;
            }
        });

        addressInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();

                const address = addressInput.value.trim();
                if (address.length > 3) {
                    this.geocodeAddress(address).then(result => {
                        if (result) {
                            latitudeInput.value = result.latitude;
                            longitudeInput.value = result.longitude;
                            this.addMarker(result.latitude, result.longitude, true);

                            // Always update with formatted address
                            addressInput.value = result.formatted_address;
                        } else {
                            latitudeInput.value = '';
                            longitudeInput.value = '';
                            if (this.marker) {
                                this.map.removeLayer(this.marker);
                            }
                        }
                    });
                }
            }
        });

        addressInput.addEventListener('blur', async () => {
            const address = addressInput.value.trim();
            if (address.length > 3) {
                const result = await this.geocodeAddress(address);
                if (result) {
                    latitudeInput.value = result.latitude;
                    longitudeInput.value = result.longitude;
                    this.addMarker(result.latitude, result.longitude, true);

                    // Always update with formatted address
                    addressInput.value = result.formatted_address;
                } else {
                    latitudeInput.value = '';
                    longitudeInput.value = '';
                    if (this.marker) {
                        this.map.removeLayer(this.marker);
                    }
                }
            }
        });
    }

    setupDisplayMap(mapContainerId, latitude, longitude, businessName) {
        if (!latitude || !longitude) {
            document.getElementById(mapContainerId).innerHTML = '<p class="text-frappe-subtext1 text-center py-8">No location available</p>';
            return;
        }

        this.initializeMap(mapContainerId, latitude, longitude);
        this.addMarker(latitude, longitude);

        if (this.marker && businessName) {
            this.marker.bindPopup(businessName).openPopup();
        }
    }
}

window.geocodingMaps = new GeocodingMaps();

document.addEventListener('DOMContentLoaded', () => {
    const businessFormMap = document.getElementById('business-form-map');
    if (businessFormMap) {
        window.geocodingMaps.setupAddressGeocoding('address', 'latitude', 'longitude', 'business-form-map');
    }

    const businessShowMap = document.getElementById('business-show-map');
    if (businessShowMap) {
        const lat = businessShowMap.dataset.latitude;
        const lng = businessShowMap.dataset.longitude;
        const name = businessShowMap.dataset.businessName;

        if (lat && lng) {
            window.geocodingMaps.setupDisplayMap('business-show-map', parseFloat(lat), parseFloat(lng), name);
        }
    }
});
