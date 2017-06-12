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
        $status = true;
        $message = "";
        $id = "";
        /**
         * @var PDO $db
         */
        $db = $this->db;
        if (empty($_REQUEST['id'])) {
            $st = $db->prepare('insert into notes (item_id,content) value (:item_id , :content)');
            $st->bindValue(':item_id', intval($_REQUEST['item_id']), PDO::PARAM_INT);
            $st->bindValue(':content', $_REQUEST['content'], PDO::PARAM_STR);
            $status = $st->execute();
            if ($status) {
                $id = $db->lastInsertId();
            } else {
                $message = $st->errorInfo();
            }
        } else {
            $sql = 'update notes set content = :content where id = :id';
            $param = [
                ':id' => $_REQUEST['id'],
                ':content' => $_REQUEST['content'],
            ];
//     array_map() expects parameter 1 to be a valid callback, non-static method PDO::quote() cannot be called statically
//            可以工作,先在看来,对非字符串通常不作任何处理
            $param = array_map([$db, 'quote'], $param);
            $sql = strtr($sql, $param);
            $status = $db->exec($sql);
        }
        Ajax::json($status, $id, $message);
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
        $note->item_id = $_REQUEST['itemId'];
        l(['type_of_item_id' => $note->item_id]);;
        $status = $note->update(false);
        l(['status' => $status]);;
        Ajax::json($status);
    }
}