(function () {

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
     * @constructor
     */
    TwGeoselect = function () {
        this.elements = [];
        var elements = document.querySelectorAll('.Geoselect');
        for (var i = 0, len = elements.length; i < len; i++) {
            this.elements.push(new TwGeoselectElement(elements[i]));
        }
    };

    /**
     *
     * Enhance a single TYPO3 Geoselect form field with autocomplete etc.
     * @constructor
     *
     * @param element
     * @returns {null}
     * @constructor
     */
    TwGeoselectElement = function (container) {
        // Set up all elements, cancel if something important was not found
        this.container = container || null;
        if (!this.container || !this.container.id) {
            return null;
        }

        this.search = document.getElementById(this.container.id + '-search');
        this.latLon = document.getElementById(this.container.id + '-lat-lon');
        if (!this.search || !this.latLon) {
            return null;
        }

        this.position = document.getElementById(this.container.id + '-position');
        this.submit = document.getElementById(this.container.id + '-submit');
        this.map = null;
        this.mapMarker = null;

        // Initalize enhancements
        this.initAutocomplete();
        this.initMap();

        // Change markup and css classes to signal that everything works
        this.onInitialize();
        return this;
    }

    /**
     * Try to add autocomplete to search field
     */
    TwGeoselectElement.prototype.initAutocomplete = function () {
        this.searchAutocomplete = new google.maps.places.Autocomplete(this.search);
        this.searchAutocomplete.addListener('place_changed', this.handle_autocompletePlaceChanged.bind(this));

        // If the focus is still on the autocomplete element,
        // we want to cancel any form submission, so the user can
        // select the autocomplete suggestions by keyboard, usually hitting enter.
        var parentForm = findAncenstorByTagName(this.search, 'form');
        parentForm.addEventListener('submit', function (event) {
            if (document.activeElement == this.search) {
                event.preventDefault();
            }
        }.bind(this));

        this.search.addEventListener('change', function (event) {
            if (!event.currentTarget.value) {
                this.reset();
            }
        }.bind(this));
    }

    /** Try to initialize the dynamic google map */
    TwGeoselectElement.prototype.initMap = function () {
        var mapElement = document.getElementById(this.container.id + '-map');
        if (!mapElement) {
            return false;
        }

        var center = {
            lat: Number(mapElement.attributes['data-latitude'].value),
            lng: Number(mapElement.attributes['data-longitude'].value)
        };


        this.map = new google.maps.Map(mapElement, {
            zoom: 6,
            center: center,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            },
            styles: [
                {
                    "featureType": "poi",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "color": "#9d9d9d"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#9d9d9d"
                        }
                    ]
                }
            ]
        });
        // this.addMyLocationControl();

        if (this.container.attributes['data-lat-lon']) {
            var latLon = this.container.attributes['data-lat-lon'].value.split(',');
            var center = {
                lat: Number(latLon[0]),
                lng: Number(latLon[1])
            }

            this.setLatLon(Number(latLon[0]), Number(latLon[1]));
        }
    }

    /**
     * Gets called when everything was set up successfully
     * and changes some CSS classes, hides elements etc.
     */
    TwGeoselectElement.prototype.onInitialize = function () {
        if (this.position) {
            this.position.parentNode.removeChild(this.position);
        }
        if (this.submit) {
            this.submit.parentNode.removeChild(this.submit);
        }
        this.container.classList.add('Geoselect--js');
        this.container.twGeoselect = this;
    }

    /**
     * Set value of latlon form field on selecting something from autocomplete
     * @returns {null}
     */
    TwGeoselectElement.prototype.handle_autocompletePlaceChanged = function (event) {
        // Cancel if there is no autocomplete result
        var place = this.searchAutocomplete.getPlace();
        if (!place || !place.geometry) {
            this.container.removeAttribute('data-lat-lon');
            return null;
        }

        // Get lat and lon
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        this.container.setAttribute('data-lat-lon', latitude + ',' + longitude);
        this.setLatLon(latitude, longitude);
    }

    /**
     *
     * @param latitude
     * @param longitude
     */
    TwGeoselectElement.prototype.setLatLon = function (latitude, longitude) {
        console.log('geoselect.js - setLatLon');

        // Set value of hidden latitude/longitude field
        this.latLon.value = (latitude + ',' + longitude);

        // Add map marker after removing the old one
        if (!this.marker) {
            var markerIconPath = this.map.getDiv().attributes['data-marker'];
            markerIconPath = markerIconPath ? markerIconPath.value : null;

            if (markerIconPath) {
                var icon = {
                    url: markerIconPath,
                    size: new google.maps.Size(48, 68),
                    scaledSize: new google.maps.Size(32, 45),
                    anchor: new google.maps.Point(12, 34)
                };
            }

            this.marker = new google.maps.Marker({
                position: { lat: latitude, lng: longitude },
                map: this.map,
                icon: icon
            });
        } else {
            this.marker.setPosition({ lat: latitude, lng: longitude });
        }

        // Set map center and zoom in
        this.map.setCenter({ lat: latitude, lng: longitude });
        this.map.setZoom(17);
        this.container.dispatchEvent(new CustomEvent('geoselect_change', {
            detail: {
                latitude: latitude,
                longitude: longitude
            }
        }));
    }

    /**
     * Reset fields and map
     */
    TwGeoselectElement.prototype.reset = function () {
        if (this.marker) {
            this.marker.setMap(null);
            this.marker = null;
            var mapElement = document.getElementById(this.container.id + '-map');
            var center = {
                lat: Number(mapElement.attributes['data-latitude'].value),
                lng: Number(mapElement.attributes['data-longitude'].value)
            };
        }

        this.map.setCenter(center);
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
    }

    /**
     * Add custom control to map for centering on the users current position
     */
    TwGeoselectElement.prototype.addMyLocationControl = function () {
        if (!navigator.geolocation) {
            return false;
        }

        var controlDiv = document.createElement('div');
        controlDiv.innerHTML = document.getElementById('GoogleMap-MyLocation').innerHTML;
        controlDiv.index = 1;
        controlDiv.addEventListener('click', function (event) {
            navigator.geolocation.getCurrentPosition(function (position) {
                this.setLatLon(position.coords.latitude, position.coords.longitude);
                this.search.value = position.coords.latitude + ',' + position.coords.longitude;
                this.map.setZoom(14);
            }.bind(this));
        }.bind(this));
        this.map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
    }

    /**
     * On DOMContentLoaded
     */
    document.addEventListener('DOMContentLoaded', function (event) {
        new TwGeoselect();
    });

})()

