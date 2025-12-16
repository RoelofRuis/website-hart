<?php

return [
    // Publieke paginas
    // Static pages
    '' => 'site/index',
    'contact' => 'site/contact',
    'avg-privacy' => 'site/avg',
    'vereniging' => 'site/association',
    'locaties' => 'site/locations',
    'auteursrecht' => 'site/copyright',
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
    'portaal/berichten' => 'teacher/messages',
    'portaal/docent/<id:\d+>' => 'teacher/update',
    'portaal/cursussen' => 'course/admin',
    'portaal/cursus/<id:\d+>' => 'course/update',
    'portaal/docenten' => 'teacher/admin',
    'portaal/docent-maken' => 'teacher/create',
    'portaal/lesvormen' => 'lesson-format/admin',
    'portaal/lesvorm/<id:\d+>' => 'lesson-format/update',
    'portaal/lesvorm-maken' => 'lesson-format/create',
    'portaal/inhoud' => 'static-content/admin',
    'portaal/inhoud/<key:[A-Za-z0-9\-]+>' => 'static-content/update'
];
