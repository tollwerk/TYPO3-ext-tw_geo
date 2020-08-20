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

$EM_CONF['tw_geo'] = [
    'title'            => 'tollwerk Geo Tools',
    'description'      => 'Geolocalization, geocoding etc.',
    'category'         => 'misc',
    'author'           => 'tollwerk GmbH',
    'author_email'     => 'info@tollwerk.de',
    'state'            => 'stable',
    'internal'         => '',
    'uploadfolder'     => '0',
    'createDirs'       => '',
    'clearCacheOnLoad' => 0,
    'version'          => '2.1.1',
    'constraints'      => [
        'depends'   => [
            'typo3'   => '10.0.0-',
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];
