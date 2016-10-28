<?php

$session = new \Phalcon\Session\Adapter\Files();

ini_set('session.cookie_domain',    $config->site->domain);
ini_set('session.name',             'ESWT_ADMIN');
ini_set('session.use_cookies',      1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly',  1);
ini_set('session.session.cookie_lifetime',  $config->app->session->cookie_lifetime);
ini_set('session.gc_maxlifetime',           $config->app->session->gc_maxlifetime);

$session->start();

return $session;

