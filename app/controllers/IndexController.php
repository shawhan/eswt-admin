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
}