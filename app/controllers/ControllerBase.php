<?php

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View;

class ControllerBase extends Controller
{
    protected $config;
    protected $user;

    // Run before method: beforeExecuteRoute
    public function onConstruct()
    {
    }

    // Run after method: beforeExecuteRoute
    public function initialize()
    {
        
    }

    public function beforeExecuteRoute($dispatcher)
    {

        $controller = $this->dispatcher->getControllerName();
        $action = $this->dispatcher->getActionName();
        $this->config = $this->di->config;

        if ($controller === 'error') {
            return true;
        }

        $auth = $this->di->get('auth');
        $user = $auth->getAuthIdentity();

        if ($controller !== 'auth' && ($controller === 'index' && $action !== 'output') ) {
            if ($user === 408) {
                $t = $this->config->app->session->cookie_lifetime / 60;
                if ($t < 60) {
                    $this->flashSession->error("閒置超過 $t 分鐘，請重新登入。");
                } else {
                    $t /= 60;
                    $this->flashSession->error("閒置超過 $t 小時，請重新登入。");
                }
                $this->user = null;
                $this->response->redirect('login');
                $this->view->disable();
                return false;
            } else if ($user === 401) {
                $this->flashSession->error("您尚未登入。");
                $this->user = null;
                $this->response->redirect('login');
                $this->view->disable();
                return false;
            }
        }

        return true;
    }

    public function indexAction()
    {
        if ($this->isDebug()) {
            //echo 'ControllerBase/indexAction<br>';
        }
    }

    protected function isDebug()
    {
        return $this->config->app->debug === true;
    }

    public function sendJsonConetnt()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader(
            'Access-Control-Allow-Origin',
            $this->config->site->access_control->allow_origin);
        $this->response->setHeader(
            'Access-Control-Allow-Methods',
            $this->config->site->access_control->allow_methods);
        $this->response->setHeader(
            'Access-Control-Allow-Headers',
            $this->config->site->access_control->allow_headers);
        $this->response->setHeader(
            'Access-Control-Max-Age',
            $this->config->site->access_control->max_age);
        $data = $this->view->getParamsToView();

        if (!is_array($data)) {
            $data = array();
        }

        if (!isset($data['error'])) {
            $data['error'] = '';
        }

        if (!isset($data['status'])) {
            $data['status'] = 200;
        }
        echo json_encode($data);
    }


}

