<?php
namespace n\modules\index\controllers;

use nxn\web\Ajax;
use PDO;

/**
 * Class AjaxController
 * @package n\modules\index
 * User: xiaoning nan
 * Date: 2017-05-{14}
 * Time: xx:xx
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
     * @return void
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
        $db = $this->db;
        $st = $db->prepare('delete from notes where id = :id');
        $st->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
        $deleted = $st->execute();
        /**
         * bindPraram 整个参数传递成为了0,导致了这个错误
         * SQL: [32] delete from notes where id = :id Params: 0
         */
        Ajax::json($deleted);
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
            $st = $db->prepare('update notes set content = :content where id = :id');
            $st->bindValue(':id', intval($_REQUEST['id']), PDO::PARAM_INT);
            $st->bindValue(':content', $_REQUEST['content'], PDO::PARAM_STR);
            $status = $st->execute();
        }
        Ajax::json($status, $id, $message);
    }


    public function moveNotePOST()
    {
        // command option value  resource
        // mv note(52) item(8)
        /**
         * @var PDO $db
         */
        $db = $this->db;
        $sql = "update notes set item_id = :itemId WHERE id = :id";
        $param = [':id'=>intval($_REQUEST['id']),':itemId'=>intval($_REQUEST['itemId'])];
        $rawSql = strtr($sql,$param);
        $status = $db->exec($rawSql);
        Ajax::json($status);
    }
}