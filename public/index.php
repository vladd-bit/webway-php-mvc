<?php

try
{
    require_once dirname(__DIR__).'/vendor/autoload.php';

    /**
     * Initialize global path resources
     */
    define('APPLICATION_FOLDER', dirname(__DIR__).'/application');
    define('CONFIG_FOLDER', APPLICATION_FOLDER. '/config');
    define('VIEWS_FOLDER', APPLICATION_FOLDER. '/views');
    define('VENDOR_FOLDER',dirname(__DIR__). '/vendor');
    define('PUBLIC_FOLDER_URL', 'http://'.$_SERVER['HTTP_HOST'].'/'.\Application\Config\WebConfig::PROJECT_NAME.'/public');
}
catch(Exception $exception)
{
   /** @noinspection PhpUnhandledExceptionInspection */
   throw new \Exception(404);
}

$router = new \Application\Core\Router();

$router->add('',['controller'=>'Home', 'action' => 'index']);
$router->add('/',['controller'=>'Home', 'action' => 'index']);
$router->add('/home/index',['controller'=>'Home', 'action' => 'index']);
$router->add('/home/testMethod', ['controller' => 'Home', 'action' => 'testMethod']);
$router->add('/home/submit',['controller'=>'Home', 'action' => 'submit', 'parameters' => ['username', 'password'] ]);

$router->dispatch($_SERVER['QUERY_STRING']);

