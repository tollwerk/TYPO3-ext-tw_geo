<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "tw_geo"
 *
 * https://github.com/tollwerk/TYPO3-ext-tw_geo
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'tollwerk Geo Tools',
    'description' => 'Geolocalization, geocoding etc.',
    'category' => 'misc',
    'author' => 'tollwerk GmbH',
    'author_email' => 'info@tollwerk.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.19-9.5.4',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
