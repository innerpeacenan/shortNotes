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
    public function postBase64(){
        var_dump($_REQUEST['note_id'],$_REQUEST['pictures']);
        die();
        $image = new Image();
        $image = $image->load(1);
        echo $image->base_b4;
    }
}
