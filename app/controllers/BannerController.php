<?php
class BannerController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function listAction()
    {

        $data = json_decode(file_get_contents('data.json'));
        foreach($data->banner as $banner) {
            $block_id = array(
                '#about' => '關於',
                '#qa' => 'Q&A',
                '#media' => '媒體',
                '#case' => '見證',
                '#info' => '資訊',
                '#activity' => '活動',
                '#location' => '地點',
                '#contact' => '聯絡我們'
            );
            if (array_key_exists($banner->url, $block_id)) {
                $banner->url = '#' . $block_id[$banner->url];
            }
        }

        $this->view->setVar('data', $data->banner);
        $this->view->pick('banner/list');
    }

    public function addAction()
    {
        $this->view->setVar('return_to','/banner/add');
        $this->view->pick('banner/add');
    }

    public function addPostAction()
    {
        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;
        if (empty($return_to)) {
            $return_to = '/banner/add';
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
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }

        switch ($type) {
            case "":
                $url = "";
                break;
            case "linkto":
                if ($linkto === "") {
                    $hasError = true;
                    $this->flashSession->error("請選擇連結區塊。");
                } else {
                    $url = $linkto;
                }
                break;
            case "other":
                if ($other === "") {
                    $hasError = true;
                    $this->flashSession->error("請輸入連結網址。");
                } else {
                    $url = $other;
                }
                break;
        }
        
        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'banner',
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
            $data->banner[] = $insert;

            file_put_contents('data.json', json_encode($data));
            

            $this->flashSession->success("新增成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function editAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->banner))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/banner', true);
        }

        $row = $data->banner[$id];

        $block_id = array(
            '#about' => '關於',
            '#qa' => 'Q&A',
            '#media' => '媒體',
            '#case' => '見證',
            '#info' => '資訊',
            '#activity' => '活動',
            '#location' => '地點',
            '#contact' => '聯絡我們'
        );
        if (array_key_exists($row->url, $block_id)) {
            $row->type = "linkto";
        } else {
            if($row->url === "") {
                $row->type = "";
            } else {
                $row->type = "other";
            }
        }

        $this->view->setVar('id', $id);
        $this->view->setVar('data', $row);
        $this->view->setVar('return_to', '/banner/edit/' . $id);
        $this->view->pick('banner/edit');
    }

    public function editPostAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->banner))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/banner', true);
        }
        $row = $data->banner[$id];

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;

        if ($this->request->hasFiles() == true && $_FILES["photo"]["name"] !== "") {
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
        } else {
            $photo_path = $row->photo;
        }

        if (empty($title)) {
            $hasError = true;
            $this->flashSession->error("請輸入標題。");
        }
        if ($sort === "" || !is_int($sort)) {
            $hasError = true;
            $this->flashSession->error("請輸入順序。");
        }
        
        switch ($type) {
            case "":
                $url = "";
                break;
            case "linkto":
                if ($linkto === "") {
                    $hasError = true;
                    $this->flashSession->error("請選擇連結區塊。");
                } else {
                    $url = $linkto;
                }
                break;
            case "other":
                if ($other === "") {
                    $hasError = true;
                    $this->flashSession->error("請輸入連結網址。");
                } else {
                    $url = $other;
                }
                break;
        }

        if($hasError){
            return $this->dispatcher->forward(array(
                'controller'    => 'banner',
                'action'        => 'edit',
            ));
        }else{
            $update = array(
                "title" => $title,
                "photo" => $photo_path,
                "url" => $url,
                "sort" => $sort,
                "create" => $row->create
            );
            $data->banner["$id"] = $update;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("編輯成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function deleteAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));

        if (!(array_key_exists($id, $data->banner))) {
            $this->flashSession->error("參數錯誤。");
        } else {
            unset($data->banner[$id]);
            $data->banner = array_values($data->banner);

            file_put_contents('data.json', json_encode($data));
            $this->flashSession->success("刪除成功。");
        }

        return $this->response->redirect("/banner", true);
    }
}

