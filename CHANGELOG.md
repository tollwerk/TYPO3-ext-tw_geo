# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Each entry will be separated into four possible groups: **Added**, **Removed**, **Changed** and **Fixed**.

## [Planned]
- Nothing right now

## [2.5.2] - 2020-09-17
### Fixed
- Restore compatibility with TYPO3 versions 8 and 9

## [2.5.1] - 2020-08-21
### Fixed
- Fix errors caused by using $GLOBALS['TSFE']->fe_user to cache the geolocation when frontend is not yet available. Use native PHP-Session instead
- Fix extneions configuration field "excludeIp" not being respected 

## [2.5.0] - 2020-08-20
### Added
- Add new field "excludeIp" to extension configuration for disabling geolocation for desired IP addresses
### Fixed
- Fix error when geolocation runs without frontend context (command line, scheduler ..)


## [2.4.0] - 2020-06-15
### Added
- Implement OpenStreetMap reverse geocoding
- Enable granular display names

## [2.3.0] - 2020-04-09
### Added
- Add GeolocationViewHelper
### Changed
- Remove old code
- Improve overall code quality


## [2.2.0] - 2020-03-01
### Added
- Geoselect form element now fires a javascript event "geoselect_change"
- Add "Center on my location" control to google map element
- Add custom Google Maps styling with `window.googleMapStyles`
- Add new form element: StaticMap
### Changed
- Refactor javascript
### Fixed
- Bugfixes for debug mode
- Bugfixes for using geocoding services in backend context like scheduler tasks
- Bugfixes for session handling and frontend/backend context 

## [2.1.0] - 2019-12-17
### Added
- Add Google Maps Autocomplete to Geoselect form field
- Add Google Map to Geoselect form field
- Add optional countries restriction to Google Maps Autocomplete
### Changed
- Reenable local javascript

## [1.4.2] - 2019-11-18
### Added
- Add TYPO3 10.x compatibility
### Changed
- Change TYPO3 version constraint to "8.7.19-10.1.99"
- Change extension state to stable

## [1.4.1] - 2019-06-07
### Fixed
- Fixed errors when trying to work with fe_user session when there is no fe_user

## [1.4.0] - 2019-04-29
### Added
- Add new field "Geoselect" for TYPO3 form framework

## [1.3.0] - 2019-04-29
### Added
- Extend `GeoUtility->geocode()` to return more than one position if desired.
- Add traversable PositionList class
- Add more properties to Positon class

## [1.2.0] - 2019-04-29
### Added
- Add new property "serviceClass" to Domain/Model/Position to show which service class was used to retrieve the position.
- Add new serice class for geocoding with Google Maps API.
### Changed
- Services for geocoding and geolocation are now chained. That means tw_geo iterates, for example, over all geocoding sercice classes until one of them returns a result or no service class is left.  

## [1.1.0] - 2019-04-24
Initial release 
