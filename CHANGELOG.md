# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Each entry will be separated into four possible groups: **Added**, **Removed**, **Changed** and **Fixed**.

## [Planned]
- Add Google Maps Autocomplete to Geoselect form field
- Add Google Map to Geoselect form field
- Add "Center on my location" control to google map element
- Geoselect element fires a javascript event "geoselect_change"

## [1.4.1] - 2019-06-07
### Bugfix
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
