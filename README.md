# TYPO3-ext-tw_geo

Right now this extension provides the following features
 * Get the user position based on the users IP address
 * Geocoding: Get position by address string
 * Add field "Geoselect" for usage in TYPO3 FormFactory classes 

All features are implemented as TYPO3 services so there is a well defined fallback behaviour
and the possibility for other developers to add their own services.

## Usage

Start by getting the instance of the GeoUtility class. 
You can do that multiple times - it's a singleton so you will only get the same instance every time. 

### Geolocation

```php
// Get location for current user IP
GeneralUtility::makeInstance(GeoUtility::class)->getGeolocation();
    
// Get location for a specific IP
GeneralUtility::makeInstance(GeoUtility::class)->getGeolocation('127.0.0.1');
```

#### Geolocation services

<dl>
    <dt><strong>Tollwerk\TwGeo\Service\Geolocation\GeoiplookupService</strong></dt>
    <dd>Uses the geoiplookup server command</dd>
    <dt><strong>Tollwerk\TwGeo\Service\Geolocation\PhpGeoIPService</strong></dt>
    <dd>Uses the PHP GeoIPService</dd>
</dl>


### Geocoding

```php
GeneralUtility::makeInstance(GeoUtility::class)->geocode('Some street, SomeCity');
```

#### Geocoding services

<dl>
    <dt><strong>Tollwerk\TwGeo\Service\Geocoding\OpenStreetMapService</strong></dt>
    <dd>Uses the OSM Nominatim web API. See https://wiki.openstreetmap.org/wiki/Nominatim</dd>
</dl>
<dl>
    <dt><strong>Tollwerk\TwGeo\Service\Geocoding\GoogleMapsService</strong></dt>
    <dd>Uses the Google Maps API. See https://developers.google.com/maps/documentation/geocoding/intro</dd>
</dl>

### New Form Elements

#### Geoselect
Usable inside FormFactory classes
```php
/** @var TYPO3\CMS\Form\Domain\Model\FormElements\Page $page */
$geoselectField = $page->createElement('yourFieldName','Geoselect');
```




## Debug configuration

In TYPO3 backend go to **Admin Tools > Settings > Extension Configuration > tw_geo**.

There you can set debug IP adresses for which fake values should be returned. 
Especially useful when devloping on  a local machine where no geolocation is possible.

There is also a frontend plugin available you can add to any page to test different features of this extension.

