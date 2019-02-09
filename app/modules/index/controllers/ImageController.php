<?php
namespace app\modules\index\controllers;

use app\models\Image;
use play\web\Controller;
use play\web\Ajax;

class ImageController extends Controller
{
    /**
     * @todo 检查用户权限(没有注册的用户不允许上传,防止用户大量上传,并且记录用户上传的文件数量,放置一个用户上传过多图片)
     */
    public function postImageSave()
    {
        $time = date('His');
        $date = date('Ymd');
        $subDir = APP_BASE_PATH . '/public/md_pictures/' . $date;
        $shortName = $time . '_' . substr($_FILES['img']['tmp_name'], 5) . $_FILES['img']['name'];
        if(!is_dir($subDir)){
            mkdir($subDir);
        }
        $fileName = $subDir . '/' . $shortName ;
        $status = move_uploaded_file($_FILES['img']['tmp_name'], $fileName);
        $row = [
            'status' => (int)$status, 'data' => [
                'url' => "http://{$_SERVER['HTTP_HOST']}/" . 'md_pictures/' . $date . '/' . $shortName
            ],
            'msg' => ''
        ];
        echo json_encode($row);
    }
}
