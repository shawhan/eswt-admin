<?php
class IndicationController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function listAction()
    {

        $data = json_decode(file_get_contents('data.json'));
        // foreach($data->indication as $indication) {
        //     var_dump($indication);
        // }

        $this->view->setVar('data', $data->indication);
        $this->view->pick('indication/list');
    }

    public function addAction()
    {
        $this->view->setVar('return_to','/indication/add');
        $this->view->pick('indication/add');
    }

    public function addPostAction()
    {
        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;
        if (empty($return_to)) {
            $return_to = '/indication/add';
        }

        if(!empty($photo_data)) {
            $photo_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo_data));

            $path = 'img/'.md5(uniqid(rand(), true)).'.png';
            file_put_contents($path, $photo_data);
            $photo_path = $this->di->config->site->url . '/'.  $path;
        } else {
            $hasError = true;
            $this->flashSession->error("請重新上傳圖片。");
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($date)) {
            $hasError = true;
            $this->flashSession->error("請輸入日期。");
        }
        $sort = (int)$sort;
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }

        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'indication',
                'action'        => 'add',
            ));
        }else{
            $data = json_decode(file_get_contents('data.json'));
            $insert = array(
                "title" => $title,
                "summary" => $summary,
                "photo" => $photo_path,
                "url" => $url,
                "sort" => $sort,
                "media" => $media,
                "date" => $date,
                "create" => date('Y-m-d H:i')
            );
            $data->indication[] = $insert;

            file_put_contents('data.json', json_encode($data));


            $this->flashSession->success("新增成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function editAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->indication))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/indication', true);
        }

        $row = $data->indication[$id];

        $this->view->setVar('id', $id);
        $this->view->setVar('data', $row);
        $this->view->setVar('return_to', '/indication/edit/' . $id);
        $this->view->pick('indication/edit');
    }

    public function editPostAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->indication))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/indication', true);
        }
        $row = $data->indication[$id];

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;

        if(!empty($photo_data)) {
            $photo_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo_data));

            $path = 'img/'.md5(uniqid(rand(), true)).'.png';
            file_put_contents($path, $photo_data);
            $photo_path = $this->di->config->site->url . '/'.  $path;
        } else {
            $photo_path = $row->photo;
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if (empty($date)) {
            $hasError = true;
            $this->flashSession->error("請輸入日期。");
        }
        $sort = (int)$sort;
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }

        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'indication',
                'action'        => 'edit',
            ));
        }else{
            $update = array(
                "title" => $title,
                "summary" => $summary,
                "photo" => $photo_path,
                "url" => $url,
                "sort" => $sort,
                "media" => $media,
                "date" => $date,
                "create" => $row->create
            );
            $data->indication["$id"] = $update;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("編輯成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function deleteAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));

        if (!(array_key_exists($id, $data->indication))) {
            $this->flashSession->error("參數錯誤。");
        } else {
            unset($data->indication[$id]);
            $data->indication = array_values($data->indication);

            file_put_contents('data.json', json_encode($data));
            $this->flashSession->success("刪除成功。");
        }

        return $this->response->redirect("/indication", true);
    }
}

