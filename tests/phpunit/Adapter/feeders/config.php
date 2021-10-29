<?php
/**
 * Nottingham Digital events.
 *
 * @see      https://github.com/pavlakis/notts-digital
 *
 * @copyright Copyright (c) 2017 Antonios Pavlakis
 * @license   https://github.com/pavlakis/notts-digital/blob/master/LICENSE (BSD 3-Clause License)
 */
return [
    'meetups' => [
        'baseUrl' => 'https://api.meetup.com',
        'uris' => [
            'events' => '/2/events/?group_urlname=%s&key=%s',
            'groups' => '/2/groups/?group_urlname=%s&key=%s',
        ],
        'PHPMinds' => [
            'group_urlname' => 'PHPMiNDS-in-Nottingham',
        ],
        'BCS-Leicester' => [
            'group_urlname' => 'BCS-Leicester',
        ],
        'Tech on Toast' => [
                'group_urlname' => 'Tech-Nottingham',
                'match' => [
                        'name' => 'Tech on Toast',
                    ],
            ],

        'Tech Nottingham' => [
                'group_urlname' => 'Tech-Nottingham',
                'match' => [
                        'name' => 'Tech Nottingham',
                    ],
            ],
    ],
    'ti.to' => [
        'baseUrl' => 'https://ti.to',
        'Design Exchange Nottingham' => [
            'url' => 'dxnevent',
        ],
    ],
];
