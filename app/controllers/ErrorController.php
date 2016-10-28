<?php

class ErrorController extends ControllerBase
{
    public function indexAction()
    {
        parent::indexAction();

        return $this->show404();
    }

    public function show404Action()
    {
        $this->response->setStatusCode(404, "Not Found");
        echo 'Not Found The URI: ' . urldecode($this->router->getRewriteUri());

        $this->showMsgForDebugMode();
    }

    public function show403Action()
    {
        $this->response->setStatusCode(403, "Forbidden");
        echo 'Permission denied: ' . urldecode($this->router->getRewriteUri());

        $this->showMsgForDebugMode();

        $this->flashSession->error("您的權限不足，請重新登入再嘗試，或請與管理員聯絡。");
        $this->response->redirect('login');
        $this->view->disable();
        return;
    }

    private function showMsgForDebugMode()
    {
        if ($this->isDebug()) {
            echo "<br>\n";
            echo '#DEBUG MESSAGE BEGIN#' . "<br>\n";
            echo 'controller: ' . $this->dispatcher->getControllerName() . "<br>\n";
            echo 'action: ' . $this->dispatcher->getActionName() . "<br>\n";
            echo '#DEBUG MESSAGE END#';
        }
    }

}

