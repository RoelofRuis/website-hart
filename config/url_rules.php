<?php

return [
    // Publieke paginas
    // Static pages
    '' => 'site/index',
    'portaal/login' => 'site/login',
    'contact' => 'static/contact',
    'avg-privacy' => 'static/avg',
    'vereniging' => 'static/association',
    'locaties' => 'static/locations',
    'auteursrecht' => 'static/copyright',
    'over-vhm' => 'static/about',
    'instrumentenverhuur' => 'static/instrument-rental',
    'jeugdfonds' => 'static/youth-fund',
    'zoeken' => 'static/search',
    'sitemap.xml' => 'site/sitemap',

    // Courses
    'cursussen' => 'course/index',
    'cursus/<slug:[A-Za-z0-9\-]+>' => 'course/view',

    // Teachers
    'docenten' => 'teacher/index',
    'docent/<slug:[A-Za-z0-9\-]+>' => 'teacher/view',

    // Files
    'bestand/<slug:.+>' => 'file/view',

    // Geauthenticeerde paginas
    // Portaal (Manage)
    'portaal' => 'site/manage',
    'portaal/logout' => 'site/logout',
    'portaal/berichten' => 'contact/messages',
    'portaal/docent/<id:\d+>' => 'teacher/update',
    'portaal/cursussen' => 'course/admin',
    'portaal/cursus/<id:\d+>' => 'course/update',
    'portaal/docenten' => 'teacher/admin',
    'portaal/docent-maken' => 'teacher/create',
    'portaal/lesvormen' => 'lesson-format/admin',
    'portaal/lesvorm/<id:\d+>' => 'lesson-format/update',
    'portaal/lesvorm-maken' => 'lesson-format/create',
    'portaal/lesvorm-kopieren' => 'lesson-format/copy',
    'portaal/inhoud' => 'static-content/admin',
    'portaal/inhoud/<key:[A-Za-z0-9\-]+>' => 'static-content/update'
];
