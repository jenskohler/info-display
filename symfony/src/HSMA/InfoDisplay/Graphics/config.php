<?php


#define('SITE_ROOT', __DIR__);
// Configuration file for the ePaper-Display application
$config = [
    // The timezone
    'timezone' => 'Europe/Berlin',
    // URL of the REST interface providing booking information
    'bookingURL' => 'https://intern.informatik.hs-mannheim.de',
    // URL of the REST interface of the ePaper server
    'ePaperServer' => 'http://141.19.141.24:8001',
    // Directory for generated image files
    'imagePath' => '/home/jens/temp/epaper',
    // Template picture for the sign
    'template' => '/home/jens/temp/background.png',
    // fonts used
    'fonts' => [
        'gd6x12' => 'resources/fonts/gd/X_6x12_LE.gdf',
        'gd10x20' => 'resources/fonts/gd/X_10x20_LE.gdf',
        'gd11x19' => 'resources/fonts/gd/Luc_11x19_LE.gdf',
        'ttfNormal' => 'resources/fonts/ttf/FiraSans-Book.ttf',
        'ttfBold' => 'resources/fonts/ttf/FiraSans-Medium.ttf',
    ],
    // Blocks in the time table
    'blocks' => [
        0 => 'vor 8:00 Uhr',
        1 => '8:00-9:30 Uhr',
        2 => '9:45-11:15 Uhr',
        3 => '11:30-13:30 Uhr',
        4 => '13:40-15:10 Uhr',
        5 => '15:20-16:50 Uhr',
        6 => '17:00-18:30 Uhr',
        7 => 'ab 18:30 Uhr',
    ],
    // Update frequencies
    'updates' => [
        'lab' => [
            '07:00',
            '08:00',
            '09:45',
            '11:30',
            '13:40',
            '15:20',
            '17:00',
            '18:30',
            '19:00',
        ],
        'meeting' => [
            '07:00',
            '08:00',
            '08:30',
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '12:00',
            '12:30',
            '13:00',
            '13:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
            '16:00',
            '16:30',
            '17:00',
            '17:30',
            '18:00',
            '18:30',
            '19:00',
        ],
    ],
    // Room data
    'rooms' => [
        'A005' => [
            'name' => 'Raskin',
            'type' => 'lab',
            'display' => 'D1010DE3',
            'enabled' => true,
        ],
        'A008' => [
            'name' => 'Dijkstra',
            'type' => 'lab',
            'display' => 'D1010893',
            'enabled' => true,
        ],
        'A010' => [
            'name' => 'Noether',
            'type' => 'lab',
            'display' => 'D1010D85',
            'enabled' => true,
        ],
        'A012' => [
            'name' => 'Gray',
            'type' => 'lab',
            'display' => 'D1010D03',
            'enabled' => true,
        ],
        'A012a' => [
            'name' => 'Wingert',
            'type' => 'lab',
            'display' => 'D1010D04',
            'enabled' => true,
        ],
        'A105a' => [
            'name' => 'Jobs',
            'type' => 'meeting',
            'display' => 'D1010DC4',
            'enabled' => true,
        ],
        'A107' => [
            'name' => 'Turing',
            'type' => 'lab',
            'display' => 'D10108F4',
            'enabled' => true,
        ],
        'A108' => [
            'name' => 'Nygaard',
            'type' => 'lab',
            'display' => 'D1010DC2',
            'enabled' => true,
        ],
        'A111' => [
            'name' => 'Shannon',
            'type' => 'lab',
            'display' => 'D1010DE2',
            'enabled' => true,
        ],
        'A205' => [
            'name' => 'Ritchie',
            'type' => 'lab',
            'display' => '',
            'enabled' => true,
        ],
        'A212a' => [
            'name' => 'von Neumann',
            'type' => 'meeting',
            'display' => 'D1010873',
            'enabled' => true,
        ],
        'L303' => [
            'name' => 'Zuse',
            'type' => 'lab',
            'display' => '',
            'enabled' => true,
        ],
    ],
    // Texts for localization
    'texts' => [
        'lastUpdate' => 'Aktualisiert',
        'hours' => 'Uhr',
        'vacant' => 'Raum ist zur Zeit nicht belegt',
        'nextSession' => 'Nächste Veranstaltung',
        'cancelled' => 'Veranstaltung fällt aus',
        'freeSlot' => '<Raum ist nicht belegt>',
    ],
];
