<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */

namespace n\modules\index\controllers;

use n\models\Image;
use nxn\web\Controller;
use nxn\web\Ajax;

class ImageController extends Controller
{
    /**
     * @todo 检查用户权限(没有注册的用户不允许上传,防止用户大量上传,并且记录用户上传的文件数量,放置一个用户上传过多图片)
     */
    public function postBase64()
    {
        $time = date('His');
        $date = date('Ymd');
        $subDir = N_APPLICATION . '/public/web/md_pictures/' . $date;
        $shortName = $time . '_' . substr($_FILES['img']['tmp_name'], 5) . $_FILES['img']['name'];
        if(!is_dir($subDir)){
            mkdir($subDir);
        }
        $fileName = $subDir . '/' . $shortName ;
        $status = move_uploaded_file($_FILES['img']['tmp_name'], $fileName);
        $row = [
            'status' => (int)$status, 'data' => [
                'url' => "http://{$_SERVER['HTTP_HOST']}/" . 'web/md_pictures/' . $date . '/' . $shortName
            ],
            'msg' => ''
        ];
        echo json_encode($row);
    }

    /**
     * @deprecated
     */
    public function _postBase64()
    {
        $imagesInDb = Image::findNoteImages($_REQUEST['note_id']);
        $imagesInDb = array_column($imagesInDb, null, 'index');
        $image = new Image();
        $index = $_REQUEST['index'];
        // 图片一般不更新,之前存过就不存了
        $status = 1;
        if (!isset($imagesInDb[$index])) {
            $image->note_id = $_REQUEST['note_id'];
            $image->base64 = $_REQUEST['base64'];
            $image->index = $index;
            $status = $image->save();
        }
        Ajax::json($status, ['id', $image->id]);
    }
}
