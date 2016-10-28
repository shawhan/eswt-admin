<?php
class ActivityController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function listAction()
    {

        $data = json_decode(file_get_contents('data.json'));
        // foreach($data->activity as $activity) {
        //     var_dump($activity);
        // }

        $this->view->setVar('data', $data->activity);
        $this->view->pick('activity/list');
    }

    public function addAction()
    {
        $this->view->setVar('return_to','/activity/add');
        $this->view->pick('activity/add');
    }

    public function addPostAction()
    {
        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;
        if (empty($return_to)) {
            $return_to = '/activity/add';
        }

        if ($this->request->hasFiles() == true) {
            $isUploaded = false;
            foreach ($this->request->getUploadedFiles() as $file) {
                $path = 'img/'. md5(uniqid(rand(), true)) . '-' .$file->getName();
                if ($file->moveTo($path)) {
                    $isUploaded = true;
                }

                if ($isUploaded == false) {
                    $hasError = true;
                    $this->flashSession->error("請重新上傳圖片。");
                }

                $photo_path = $this->di->config->site->url . '/'.  $path;
            }
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }
        
        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'activity',
                'action'        => 'add',
            ));
        }else{
            $data = json_decode(file_get_contents('data.json'));
            $insert = array(
                "title" => $title,
                "photo" => $photo_path,
                "url" => $url,
                "sort" => $sort,
                "create" => date('Y-m-d H:i')
            );
            $data->activity[] = $insert;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("新增成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function editAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->activity))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/activity', true);
        }

        $row = $data->activity[$id];

        $this->view->setVar('id', $id);
        $this->view->setVar('data', $row);
        $this->view->setVar('return_to', '/activity/edit/' . $id);
        $this->view->pick('activity/edit');
    }

    public function editPostAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->activity))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/activity', true);
        }
        $row = $data->activity[$id];

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }
        
        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'activity',
                'action'        => 'edit',
            ));
        }else{
            $update = array(
                "title" => $title,
                "photo" => $row->photo,
                "url" => $url,
                "sort" => $sort,
                "create" => $row->create
            );
            $data->activity["$id"] = $update;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("編輯成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function deleteAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));

        if (!(array_key_exists($id, $data->activity))) {
            $this->flashSession->error("參數錯誤。");
        } else {
            unset($data->activity[$id]);
            $data->activity = array_values($data->activity);

            file_put_contents('data.json', json_encode($data));
            $this->flashSession->success("刪除成功。");
        }

        return $this->response->redirect("/activity", true);
    }
}

