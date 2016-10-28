<?php

class AuthController extends ControllerBase
{
    public function indexAction()
    {
        parent::indexAction();
    }

    public function loginAction()
    {
        $return_to = $this->request->getPost('return_to');
        if (empty($return_to)) {
            $return_to = $this->router->getRewriteUri();
            if (strpos($return_to,'/login') === 0) {
                $return_to = '/';
            }
            $return_to = $this->url->get(ltrim($return_to, '/'));
        }
        
        $this->view->setVar('return_to', $return_to);
        $this->view->pick('login');
    }

    public function loginPostAction()
    {
        if ($this->security->checkToken()) {
            $auth = $this->di->get('auth');

            $login = $this->request->getPost('user_login');
            $password = $this->request->getPost('user_pass');
            $return_to = $this->request->getPost('return_to');

            if ($login === "eswt-admin" && $password === "12345678") {
                $auth->setAuthIdentity();
                return $this->response->redirect($return_to, true);
            } else {
                $this->flashSession->error("使用者帳號或密碼錯誤。");
                return $this->dispatcher->forward(array(
                    'controller'    => 'auth',
                    'action'        => 'login'
                ));
            }
        } else {
            return $this->response->redirect(array('for'=>'auth-login'));
        }
    }

    public function logoutAction()
    {
        $this->di->get('auth')->deauth();
        $this->flashSession->notice("你已成功登出管理系統。");
        return $this->response->redirect(array('for'=>'auth-login'));
    }
}

