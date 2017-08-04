<?php
namespace n\modules\index\controllers;

use n\models\Notes;
use nxn\web\Ajax;
use PDO;

/**
 * Class AjaxController
 * @package n\modules\index
 * User: xiaoning nan
 * date:2017-05-24
 * time:08:24:16
 * Description: description
 */
class NoteController
{
    public $db;


    /**
     * AjaxController constructor.
     */

    public function __construct()
    {
        $dbconfig = \N::$app->conf['db'];
        $this->db = \N::createObject($dbconfig);
    }

    /**
     * @access
     * @return string
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 暂且修改为彻底删除
     */
    public function deleteNotePOST()
    {
        /**
         * @var PDO $db
         */
        $note = (new Notes())->load($_REQUEST['id']);
        if(null === $note){
           Ajax::json(false,[],'can not find related note wiht id:'.$_REQUEST['id']) ;
        }
        $deleted =  $note->delete();
        return Ajax::json($deleted);
    }


    public function saveNotePOST()
    {
        $message = "";
        $note = (new Notes());
        $note->setAttributes($_REQUEST);
        $status = $note->save(false);
        $id = $note->id;
        Ajax::json($status, ['id'=>$id], $message);
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * You cannot serialize or unserialize PDO instances ?? 怎么回事?
     */
    public function moveNotePOST()
    {
        // mv note(52) item(8)
        $note = (new Notes())->load($_REQUEST['id']);
        if ($note === null) {
            Ajax::json(false);
            return;
        }
        $note-> scenario = 'update';
        $note->setAttributes($_REQUEST);
        l(['type_of_item_id' => $note->item_id]);;
        $status = $note->update(false);
        l(['status' => $status]);
        Ajax::json($status);
    }
}