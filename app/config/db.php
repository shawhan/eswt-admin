<?php

$conn = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
    'host'      => $config->database->host,
    'port'      => $config->database->port,
    'username'  => $config->database->username,
    'password'  => $config->database->password,
    'dbname'    => $config->database->dbname,
    "dialectClass" => '\Phalcon\Db\Dialect\MysqlExtended',
    'options'   => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        PDO::ATTR_EMULATE_PREPARES  => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    )
));

if ($config->app->debug) {
    $eventsManager = new \Phalcon\Events\Manager();
    $logger = new \Phalcon\Logger\Adapter\File(APP_PATH . '/logs/database.log');
    $eventsManager->attach('db', function($event, $conn) use ($logger) {
        if ($event->getType() == 'beforeQuery') {
            $logger->log($conn->getSQLStatement(), \Phalcon\Logger::INFO);
            $vars = $conn->getSQLVariables();
            if (count($vars) > 0) {
                $logger->log('(' . implode(', ', 
                    array_map(function ($v, $k) { 
                        return sprintf("%s => '%s'", $k, $v); 
                    }, $vars, array_keys($vars))
                ) . ')', \Phalcon\Logger::INFO);
            }
        }
    });

    $conn->setEventsManager($eventsManager);
}

return $conn;

