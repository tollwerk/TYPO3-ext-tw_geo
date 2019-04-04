<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function() {
        // Add static TypoScript files
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            'tw_geo',
            'Configuration/TypoScript/Static',
            'tollwerk Geo Tools'
        );

        // Register plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Tollwerk.TwGeo',
            'Debug',
            'LLL:EXT:tw_geo/Resources/Private/Language/locallang_db.xlf:plugin.debug',
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('tw_geo').'Resources/Public/Icons/Backend/Earth.png'
        );

        // Register flexforms for plugins
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['twgeo_debug'] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('twgeo_debug','FILE:EXT:tw_geo/Configuration/FlexForm/Debug.xml');
    }
);
