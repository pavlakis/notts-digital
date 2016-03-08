<?php
/**
 * Nottingham Digital events
 *
 * @link      https://github.com/pavlakis/notts-digital
 * @copyright Copyright (c) 2016 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
return [
    "meetups" => [
        'baseUrl' => 'https://api.meetup.com/2/events/?group_urlname=%s&key=%s',
        'PHPMinds' => [
            'group_urlname' => 'PHPMiNDS-in-Nottingham'
        ]
    ],
    'ti.to' => [
        'baseUrl' => 'https://ti.to',
        'Design Exchange Nottingham' => [
            'url' => 'dxnevent'
        ]
    ]
];