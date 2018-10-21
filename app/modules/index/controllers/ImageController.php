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
    public function postBase64()
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
