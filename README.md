# TYPO3-ext-tw_geo

Right now this extension provides the following features
 * Get the user position based on the users IP address
 
All features are implemented as TYPO3 services so there is a well defined fallback behaviour.

## Debug configuration

In TYPO3 backend go to **Admin Tools > Settings > Extension Configuration > tw_geo**.

There you can set debug IP adresses for which fake values should be returned. 
Especially useful when devloping on  a local machine where no geolocation is possible. 
 


## Usage

Start by getting the instance of the GeoUtility class. 
You can do that multiple times - it's a singleton so you will only the the same instance every time. 

### Geolocation

```php
// Get location for current user IP
GeneralUtility::makeInstance(GeoUtility::class)->getGeolocation();
    
// Get location for a specific IP
GeneralUtility::makeInstance(GeoUtility::class)->getGeolocation('127.0.0.1');
```
