<?php
/**
 * [
 *      'GET' => '/path', // Method and path, currently supports POST | GET
 *      'use' => '\FQCN\Controller'
 *      'call' => 'scrap', // method to call in controller
 *      'title' => 'descriptive title for presentation'
 * ]
 */
return [
    [
        'GET' => '/',
        'use' => '\AGustavo87\WebCollector\Controllers\PresentController',
        'call' => 'present'
    ],
    [
        'GET' => '/scrap',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'scrap',
        'title' => 'Scrap some stuff'
    ],
    [
        'GET' => '/meta',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'meta',
        'title' => 'Get some meta parameters'
    ],
    [
        'GET' => '/grab',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'grab',
        'title' => 'Grab a page for later analysis'
    ],

    [
        'GET' => '/stored',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'stored'
    ],

    [
        'POST' => '/xpath',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'xpath'
    ],

    [
        'GET' => '/analize',
        'use' => '\AGustavo87\WebCollector\Controllers\ScrapperController',
        'call' => 'analize',
        'title' => 'Analize a previously stored page'
    ],

    [
        'GET' => '/moodle/authenticate',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'showAuthenticate',
        'title' => 'Authenticate in Moodle'
    ],

    [
        'POST' => '/moodle/authenticate',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'authenticate',
    ],

    [
        'GET' => '/moodle/grab',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'showGrab',
    ],

    [
        'POST' => '/moodle/grab',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'grab',
    ],

    [
        'GET' => '/moodle/studentsinfo',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'showStudentsInfo',
        'title' => 'Get data of enrolled students in a course.'
    ],
    [
        'GET' => '/moodle/forumparticipations',
        'use' => '\AGustavo87\WebCollector\Controllers\MoodleScrapController',
        'call' => 'showForumParticipations',
        'title' => 'Get data of participations of students in forums.'
    ],
];