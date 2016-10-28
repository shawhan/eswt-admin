<?php

return new \Phalcon\Config(array(
    'site' => array(
        'name'          => 'ShockWave管理後台',
        'domain'        => '',
        'url'           => '',
    ),
    'app' => array(
        'configDir'         => APP_PATH . '/config/',
        'controllersDir'    => APP_PATH . '/controllers/',
        'modelsDir'         => APP_PATH . '/models/',
        'viewsDir'          => APP_PATH . '/views/',
        'pluginsDir'        => APP_PATH . '/plugins/',
        'libraryDir'        => APP_PATH . '/library/',
        'formDir'           => APP_PATH . '/forms/',
        'helperDir'         => APP_PATH . '/helper/',
        'cacheDir'          => APP_PATH . '/cache/',
        'logDir'            => APP_PATH . '/logs/',
        'uploadDir'         => '',
        'dev'               => array(
            'staticBaseUri'     => '/',
            'baseUri'           => '/',
        ),
        'product'           => array(
            'staticBaseUri'     => '/',
            'baseUri'           => '/',
        ),
        'debug'             => false,
        'baseUri'           => '/',
        'session'           => array(
            'cookie_lifetime'   => 86400,
            'gc_maxlifetime'    => 86400,
        ),
        'access_control'    => array(
            'allow_origin'      => '*',
            'allow_methods'     => 'POST, GET, OPTIONS',
            'allow_headers'     => 'X-PINGOTHER',
            'max_age'           => '86400',
        ),
    ),
));

