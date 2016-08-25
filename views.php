<?php

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}
return [
    // viewtype
    'default' => [
       'js/fullcalendar.js' => $composer_path . 'vendor/bower-asset/fullcalendar/dist/fullcalendar.min.js',
       'js/moment.js' => $composer_path . 'vendor/bower-asset/moment/min/moment-with-locales.min.js',
       'css/event_manager/fullcalendar.css' => $composer_path . 'vendor/bower-asset/fullcalendar/dist/fullcalendar.min.css',
    ],
];