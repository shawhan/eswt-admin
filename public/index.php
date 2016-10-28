<?php

ini_set('date.timezone','Asia/Taipei');
define('APP_PATH', realpath('../app'));

$config = APP_PATH . '/config/config.php';
if (file_exists($config)) {
    $config = include $config;
} else {
    die('Can not found config.php');
}
if ($config->app->debug) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
// Check config
$checkConfig = true;
if (!is_writable($config->app->logDir)) {
    $checkConfig = false;
    echo "Please running script first: sudo ./fix-file-permission.sh<br>\n";
}
if (!isset($config->app->session)) {
    $checkConfig = false;
    echo "Not found: app->session in app/config.php<br>\n";
}
if (!isset($config->app->access_control)) {
    $checkConfig = false;
    echo "Not found: app->access_control in app/config.php<br>\n";
}
if (!$checkConfig) {
    die();
}
unset ($checkConfig);

try {
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        $config->app->controllersDir,
        $config->app->modelsDir,
        $config->app->libraryDir,
        $config->app->helperDir
    ))->register();

    $di= new \Phalcon\DI\FactoryDefault();
    $di->set('view', function() use ($config) {
        return include $config->app->configDir . 'view.php';
    });
    $di->set('router', function () use ($config) {
        return include $config->app->configDir . 'routes.php';
    });
    $di->setShared('session', function() use ($config) {
        return include $config->app->configDir . 'session.php';
    });
    $di->setShared('security', function() use ($config) {
        return include $config->app->configDir . 'security.php';
    });
    $di->set('url', function() use ($config) {
        $url = new \Phalcon\Mvc\Url();
        $url->setBaseUri( '//' . $config->site->domain . '/');
        return $url;
    });
    $di->setShared('auth', function() use ($di) {
        $auth = new Auth($di);
        return $auth;
    });
    $di->set('flash', function(){
        $flash = new \Phalcon\Flash\Direct(array(
            'error'     => 'bg-danger',
            'warning'   => 'bg-warning',
            'success'   => 'bg-success',
            'notice'    => 'bg-info'
        ));
        return $flash;
    });
    $di->set('flashSession', function(){
        $flashSession = new \Phalcon\Flash\Session(array(
            'error'     => 'alert bg-danger',
            'warning'   => 'alert bg-warning',
            'success'   => 'alert bg-success',
            'notice'    => 'alert bg-info'
        ));
        return $flashSession;
    });
    $di->config = $config;

    $application = new \Phalcon\Mvc\Application();
    $application->setDI($di);
    // $application->useImplicitView(false);

    echo $application->handle()->getContent();

    // if ($config->app->debug) {
    //     echo '<pre>';
    //     print_r($_SERVER);
    //     echo '</pre>';
    // }
} catch (\Phalcon\Exception $e) {
    $logger = new \Phalcon\Logger\Adapter\File(APP_PATH . '/logs/error.log');
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());

    $response = new \Phalcon\Http\Response();
    $response->setStatusCode(200, 'Q_Q');
    $response->setContent(
       'Sorry Q_Q' . '<br>' .
       'Error code:' . $e->getCode() . '<br>' .
       'File: ' . $e->getFile() . '<br>' .
       'Line: ' . $e->getLine() . '<br>' .
       'Trace: ' . $e->getTrace() . '<br>'.
       'Message: ' . $e->getMessage() . '<br>' .
       'TraceAsString: <br>' . nl2br($e->getTraceAsString()) . '<br>'
    );

    $response->send();
}

