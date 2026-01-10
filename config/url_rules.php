<?php

return [
    // Publieke paginas
    // Static pages
    '' => 'site/index',
    'portaal/login' => 'site/login',
    'zoeken' => 'site/search',
    'sitemap.xml' => 'site/sitemap',
    'contact' => 'static/contact',
    'avg-privacy' => 'static/avg',
    'vereniging' => 'static/association',
    'locaties' => 'static/locations',
    'auteursrecht' => 'static/copyright',
    'over-vhm' => 'static/about',
    'instrumentenverhuur' => 'static/instrument-rental',
    'jeugdfonds' => 'static/youth-fund',

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
    'portaal/cursussen' => 'course/admin',
    'portaal/cursus/<id:\d+>' => 'course/update',
    'portaal/gebruikers' => 'user/admin',
    'portaal/gebruiker/<id:\d+>' => 'user/update',
    'portaal/gebruiker-maken' => 'user/create',
    'portaal/lesvormen' => 'lesson-format/admin',
    'portaal/lesvorm/<id:\d+>' => 'lesson-format/update',
    'portaal/lesvorm-maken' => 'lesson-format/create',
    'portaal/lesvorm-kopieren' => 'lesson-format/copy',
    'portaal/inhoud' => 'static-content/admin',
    'portaal/inhoud/<id:\d+>' => 'static-content/update',
    'portaal/categorieen' => 'category/index',
    'portaal/categorie-maken' => 'category/create',
    'portaal/categorie/<id:\d+>' => 'category/update',
];
