<?php

class IndexController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function indexAction()
    {
        parent::indexAction();
        return $this->response->redirect('banner');
    }

    public function outputAction()
    {
    	$http_origin = $_SERVER['HTTP_ORIGIN'];
    	$allow_http_origin = array(
		    'http://shockwave.com.tw',
		    'https://shawhan.github.io',
		    'http://127.0.0.1:24681'
		);

		if (in_array($http_origin, $allow_http_origin)) {
		    header("Access-Control-Allow-Origin: $http_origin");
		}

		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: X-PINGOTHER");
		header("Access-Control-Max-Age: 1728000");

		$data = file_get_contents('data.json');
		echo $data;
    }
}