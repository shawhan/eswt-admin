<?php

$view = new \Phalcon\Mvc\View();
//Disable several levels
$view->disableLevel(array(
    \Phalcon\Mvc\View::LEVEL_LAYOUT => true,
    \Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT => true
));
$view->setViewsDir($config->app->viewsDir);
$view->registerEngines(array(
    // '.phtml' => function($view, $di) {
    //     $phtml = new \Phalcon\Mvc\View\Engine\Php($view, $di);
    //     //set some options here
    //     return $phtml;
    // }
    '.phtml'    => '\Phalcon\Mvc\View\Engine\Php',
));

return $view;

