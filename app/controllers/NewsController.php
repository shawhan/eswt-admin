<?php
class NewsController extends ControllerBase
{
    public function onConstruct()
    {
        parent::onConstruct();
    }

    public function listAction()
    {
        $data = json_decode(file_get_contents('data.json'));
        foreach($data->news as $news) {
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
            if (array_key_exists($news->url, $block_id)) {
                $news->url = '#' . $block_id[$news->url];
            }
        }

        $this->view->setVar('data', $data->news);
        $this->view->pick('news/list');
    }

    public function addAction()
    {
        $this->view->setVar('return_to','/news/add');
        $this->view->pick('news/add');
    }

    public function addPostAction()
    {
        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;
        if (empty($return_to)) {
            $return_to = '/news/add';
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
                'controller'    => 'news',
                'action'        => 'add',
            ));
        }else{
            $data = json_decode(file_get_contents('data.json'));
            $insert = array(
                "title" => $title,
                "url" => $url,
                "sort" => $sort,
                "create" => date('Y-m-d H:i')
            );
            $data->news[] = $insert;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("新增成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function editAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->news))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/news', true);
        }

        $row = $data->news[$id];
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
        $this->view->setVar('return_to', '/news/edit/' . $id);
        $this->view->pick('news/edit');
    }

    public function editPostAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));
        if (!(array_key_exists($id, $data->news))) {
            $this->flashSession->error("參數錯誤。");
            return $this->response->redirect('/news', true);
        }
        $row = $data->news[$id];

        $postdata = $this->request->getPost();
        extract($postdata, EXTR_SKIP);
        $hasError = false;

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
                'controller'    => 'news',
                'action'        => 'edit',
            ));
        }else{
            $update = array(
                "title" => $title,
                "url" => $url,
                "sort" => $sort,
                "create" => $row->create
            );
            $data->news["$id"] = $update;

            file_put_contents('data.json', json_encode($data));

            $this->flashSession->success("編輯成功。");
            return $this->response->redirect($return_to, true);
        }
    }

    public function deleteAction($id)
    {
        $data = json_decode(file_get_contents('data.json'));

        if (!(array_key_exists($id, $data->news))) {
            $this->flashSession->error("參數錯誤。");
        } else {
            unset($data->news[$id]);
            $data->news = array_values($data->news);

            file_put_contents('data.json', json_encode($data));
            $this->flashSession->success("刪除成功。");
        }

        return $this->response->redirect("/news", true);
    }
}

