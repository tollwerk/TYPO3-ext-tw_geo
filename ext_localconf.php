<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(
    function() {
        // Register or change services
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'tw_geo',
            'geocoding',
            \Tollwerk\TwGeo\Service\Geocoding\OpenStreetMapService::class,
            [
                'title' => 'OpenStreetMap',
                'description' => 'Uses the OSM Nominatim web API',
                'subtype' => '',
                'available' => true,
                'priority' => 50,
                'quality' => 50,
                'os' => '',
                'exec' => '',
                'className' => \Tollwerk\TwGeo\Service\Geocoding\OpenStreetMapService::class
            ]
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'tw_geo',
            'geocoding',
            \Tollwerk\TwGeo\Service\Geocoding\GoogleMapsService::class,
            [
                'title' => 'Google Maps',
                'description' => 'Uses the Google Maps web API',
                'subtype' => '',
                'available' => true,
                'priority' => 75,
                'quality' => 75,
                'os' => '',
                'exec' => '',
                'className' => \Tollwerk\TwGeo\Service\Geocoding\GoogleMapsService::class,
            ]
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'tw_fh',
            'geolocation',
            \Tollwerk\TwGeo\Service\Geolocation\PhpGeoIPService::class,
            [
                'title' => 'PHP GeoIP extension',
                'description' => 'Uses PHP geoip_record_by_name() function',
                'subtype' => '',
                'available' => true,
                'priority' => 50,
                'quality' => 50,
                'os' => '',
                'exec' => '',
                'className' => \Tollwerk\TwGeo\Service\Geolocation\PhpGeoIPService::class
            ]
        );
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
            'tw_fh',
            'geolocation',
            \Tollwerk\TwGeo\Service\Geolocation\GeoiplookupService::class,
            [
                'title' => 'geoiplookup',
                'description' => 'Uses geoiplookup shell command',
                'subtype' => '',
                'available' => true,
                'priority' => 60,
                'quality' => 50,
                'os' => '',
                'exec' => 'geoiplookup',
                'className' => \Tollwerk\TwGeo\Service\Geolocation\GeoiplookupService::class
            ]
        );

        // Configure plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Tollwerk.TwGeo',
            'Debug',
            ['Debug' => 'geolocation, geocoding'],
            ['Debug' => 'geolocation, geocoding']
        );
    }
);

