(function (d) {
    /**
     * Find closest ancestor by tag name.
     * Hopefully compatible with most browsers.
     *
     * @param el    The current element
     * @param tagName   The tagName to search for
     */
    function findAncenstorByTagName(el, tagName) {
        while ((el = el.parentElement) && !(el.tagName == tagName.toLowerCase()) && !(el.tagName == tagName.toUpperCase())) ;
        return el;
    }

    /**
     * Enhance TYPO3 Geoselect form fields with autocomplete etc.
     *
     * @param {NodeList} elements Geoselect elements
     * @constructor
     */
    function Geoselect(elements) {
        this.elements = [].slice.call(elements)
            .map((e) => new GeoselectElement(e));
    }

    /**
     *
     * Enhance a single TYPO3 Geoselect form field with autocomplete etc.
     * @constructor
     *
     * @param element
     * @returns {null}
     * @constructor
     */
    function GeoselectElement(container) {
        // Set up all elements, cancel if something important was not found
        this.container = container || null;
        if (!this.container || !this.container.id) {
            return null;
        }

        this.search = d.getElementById(`${this.container.id}-search`);
        this.latLon = d.getElementById(`${this.container.id}-lat-lon`);
        if (!this.search || !this.latLon) {
            return null;
        }

        this.position = d.getElementById(`${this.container.id}-position`);
        this.submit = d.getElementById(`${this.container.id}-submit`);

        // Initalize enhancements
        this.initAutocomplete();
        this.map = this.initMap();

        // Change markup and css classes to signal that everything works
        this.onInitialize();
    }

    /**
     * Try to add autocomplete to search field
     */
    GeoselectElement.prototype.initAutocomplete = function () {
        if (this.search.hasAttribute('autocomplete')) {
            if (this.search.getAttribute('autocomplete') === 'off') {
                return null;
            }
        }

        this.searchAutocomplete = new google.maps.places.Autocomplete(this.search, {
            language: 'ru',
            componentRestrictions: {
                country: this.container.hasAttribute('data-restrict-countries') ? String(this.container.getAttribute('data-restrict-countries'))
                    .split(',')
                    .map((item) => item.trim()) : []
            }
        });
        this.searchAutocomplete.addListener('place_changed', this.handle_autocompletePlaceChanged.bind(this));

        // If the focus is still on the autocomplete element,
        // we want to cancel any form submission, so the user can
        // select the autocomplete suggestions by keyboard, usually hitting enter.

        findAncenstorByTagName(this.search, 'form')
            .addEventListener('submit', (event) => {
                event.currentTarget.classList.add('loading');
                if (d.activeElement === this.search) {
                    event.preventDefault();
                }
            });

        this.search.addEventListener('change', (event) => {
            if (!event.currentTarget.value) {
                this.reset();
            }
        });
    };

    /**
     * Default map styles
     *
     * @type {*[]}
     */
    GeoselectElement.defaultStyles = [
        {
            featureType: 'poi',
            elementType: 'labels.icon',
            stylers: [
                {
                    color: '#9d9d9d'
                }
            ]
        },
        {
            featureType: 'poi',
            elementType: 'labels.text.fill',
            stylers: [
                {
                    color: '#9d9d9d'
                }
            ]
        }
    ];

    /**
     * Try to initialize the dynamic google map
     *
     * @returns {google.maps.Map} Google Map
     */
    GeoselectElement.prototype.initMap = function () {
        const mapElement = d.getElementById(`${this.container.id}-map`);
        if (!mapElement) {
            return null;
        }

        const latLon = this.container.hasAttribute('data-lat-lon') ? this.container.attributes['data-lat-lon'].value.split(',') : [];
        const map = new google.maps.Map(mapElement, {
            zoom: 10,
            center: {
                lat: Number((latLon.length === 2) ? latLon[0] : mapElement.attributes['data-latitude'].value),
                lng: Number((latLon.length === 2) ? latLon[1] : mapElement.attributes['data-longitude'].value)
            },
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            zoomControl: false,
            mapTypeControl: false,
            zoomControlOptions: { position: google.maps.ControlPosition.LEFT_TOP },
            styles: window.googleMapStyles ? window.googleMapStyles : GeoselectElement.defaultStyles

        });
        // this.addMyLocationControl();
        return map;
    };

    /**
     * Gets called when everything was set up successfully
     * and changes some CSS classes, hides elements etc.
     */
    GeoselectElement.prototype.onInitialize = function () {
        if (this.position) {
            this.position.parentNode.removeChild(this.position);
        }
        this.container.classList.add('Geoselect--js');
        this.container.geoselect = this;
        this.container.dispatchEvent(new CustomEvent('geoselect_init'));
    };

    /**
     * Set value of latlon form field on selecting something from autocomplete
     */
    GeoselectElement.prototype.handle_autocompletePlaceChanged = function (event) {
        // Cancel if there is no autocomplete result
        const place = this.searchAutocomplete.getPlace();
        if (!place || !place.geometry) {
            this.container.removeAttribute('data-lat-lon');
            return null;
        }

        // Get lat and lon
        const latitude = place.geometry.location.lat();
        const longitude = place.geometry.location.lng();
        this.container.setAttribute('data-lat-lon', `${latitude},${longitude}`);
        this.setLatLon(latitude, longitude);
    };

    /**
     * Set latitude and longitude
     *
     * @param {Number} latitude Latitude
     * @param {Number} longitude Longitude
     */
    GeoselectElement.prototype.setLatLon = function (latitude, longitude) {
        // Set value of hidden latitude/longitude field
        this.latLon.value = (`${latitude},${longitude}`);
        this.latLon.dispatchEvent(new Event('change'));
        // If we have a Google Map
        if (this.map) {
            // Add map marker after removing the old one
            if (!this.marker) {
                let markerIconPath = this.map.getDiv().attributes['data-marker'];
                markerIconPath = markerIconPath ? markerIconPath.value : null;
                this.marker = new google.maps.Marker({
                    position: {
                        lat: latitude,
                        lng: longitude
                    },
                    map: this.map,
                    icon: markerIconPath ? {
                        url: markerIconPath,
                        size: new google.maps.Size(48, 68),
                        scaledSize: new google.maps.Size(32, 45),
                        anchor: new google.maps.Point(12, 34)
                    } : null
                });
            } else {
                this.marker.setPosition({
                    lat: latitude,
                    lng: longitude
                });
            }

            // Set map center and zoom in
            this.map.setCenter({
                lat: latitude,
                lng: longitude
            });
            this.map.setZoom(10);
        }

        this.container.dispatchEvent(new CustomEvent('geoselect_change', {
            detail: {
                latitude,
                longitude
            }
        }));
    };

    /**
     * Reset fields and map
     */
    GeoselectElement.prototype.reset = function () {
        if (this.marker) {
            this.marker.setMap(null);
            this.marker = null;
            const mapElement = d.getElementById(`${this.container.id}-map`);
            this.map.setCenter({
                lat: Number(mapElement.attributes['data-latitude'].value),
                lng: Number(mapElement.attributes['data-longitude'].value)
            });
        }

        this.map.setZoom(6);

        if (this.latLon.value) {
            this.latLon.value = '';
        }

        if (this.search.value) {
            this.search.value = '';
        }

        this.container.removeAttribute('data-lat-lon');
        this.container.dispatchEvent(new CustomEvent('geoselect_change', {
            detail: {}
        }));
    };

    /**
     * Add custom control to map for centering on the users current position
     */
    GeoselectElement.prototype.addMyLocationControl = function () {
        if (!navigator.geolocation) {
            return false;
        }

        const controlDiv = d.createElement('div');
        controlDiv.innerHTML = d.getElementById('GoogleMap-MyLocation').innerHTML;
        controlDiv.index = 1;
        controlDiv.addEventListener('click', (event) => {
            navigator.geolocation.getCurrentPosition((position) => {
                this.setLatLon(position.coords.latitude, position.coords.longitude);
                this.search.value = `${position.coords.latitude},${position.coords.longitude}`;
                this.map.setZoom(14);
            });
        });
        this.map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
    };

    /**
     * On DOMContentLoaded
     */
    d.addEventListener('DOMContentLoaded', (event) => {
        const geoselectElements = d.querySelectorAll('.Geoselect');
        if (geoselectElements.length) {
            new Geoselect(geoselectElements);
        }
    });
}(document));
