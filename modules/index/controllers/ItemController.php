<?php
namespace n\modules\index\controllers;

use n\models\Items;
use nxn\web\Ajax;
use PDO;
use nxn\db\Query;

/**
 * Class AjaxController
 * @package n\modules\index
 * User: xiaoning nan
 * Date: 2017-05-{14}
 * Time: xx:xx
 * Description: description
 */
class ItemController
{
    public $db;

    /**
     * AjaxController constructor.
     */

    public function __construct()
    {
        $this->db = \N::createObject('db');
    }

//---------------------------------Items-------------------------------------
    public function getItemsPOST()
    {
        $fid = isset($_REQUEST['fid']) ? intval($_REQUEST['fid']) : 0;
        $sql = 'select * from `items` where `fid` = :fid AND status = "enable" ORDER BY `rank` DESC';
        $param = [':fid' => $fid];
        $result = Query::all($sql, $param);
        foreach ($result as $i => $row) {
            // 给前台checkbox 用的
            $result[$i]['status'] = ($row['status'] === 'enable') ? false : true;
        }
        Ajax::json(true, $result, "success");
    }

    public function deleteItemPOST()
    {
        $message = "";
        $status = true;
        /**
         * @var PDO $db
         * db 未定义,所以直接抛出了异常
         */
        $db = $this->db;
        $db->beginTransaction();
        try {
            $st = $db->prepare('delete from notes where item_id = :item_id');
            $st->bindValue(':item_id', intval($_REQUEST['id']), PDO::PARAM_INT);
            $st->execute();
            /**
             * 之前 $st 写成了 $t,造成绑定的语句并没有执行
             */
            $st = $db->prepare('delete from items where id = :id');
            $st->bindValue(':id', intval($_REQUEST['id']), PDO::PARAM_INT);
            $st->execute();
            $db->commit();
        } catch (\PDOException $e) {
// An exception has occured, which means that one of our database queries failed.
// Print out the error message.
            $status = false;
            $message = $e->getMessage();
            // row back transaction
            $db->rollBack();
        }
        header('Content-Type:application/json; charset:utf8');
        $json = json_encode(['status' => $status, 'message' => $message]);
        echo $json;
        exit();
    }

    public function saveItemPOST()
    {
        $fid = isset($_REQUEST['fid']) ? intval($_REQUEST['fid']) : 0;
        $id = "";
        $message = "";
        /**
         * @var PDO $db
         */
        $db = $this->db;
        /**
         * @todo fuck this is source of bug to put pareInt($_REQUEST['name']) here
         */
        if ($_REQUEST['id']) {

            $st = $db->prepare('update items set name = :name WHERE id = :id');
            $st->bindValue(':name', $_REQUEST['name'], PDO::PARAM_STR);
            $st->bindValue(':id', intval($_REQUEST['id']), PDO::PARAM_INT);
            $status = $st->execute();
        } else {
            //插入,更新树结构
//            pid 找左右边界线
            $st = $db->prepare("select max(`id`) from `items` WHERE `user_id` = :userId");
            $st->bindValue(':userId', 1, PDO::PARAM_INT);
            $st->execute();
            $rank = (int)$st->fetch(PDO::FETCH_COLUMN) * 10;
            $st = $db->prepare("select * from items WHERE id = :pid");
            $st->bindValue(':pid', $fid, PDO::PARAM_INT);
            $st->execute();
            $fathor = $st->fetch(PDO::FETCH_ASSOC);
            $st = $db->prepare("select max(`t_right`) from items WHERE fid = :pid");
            $st->bindValue(':pid', $fid, PDO::PARAM_INT);
            $st->execute();
            $max_child_right = $st->fetch(PDO::FETCH_COLUMN);
            // 跟新所有上层父节点,给新插入的字节点腾出空间
            $st = $db->prepare('update items set  `t_right` = `t_right` + 2   WHERE `t_right` >= :fatherRight');
            $st->bindValue(':fatherRight', intval($fathor['t_right']), PDO::PARAM_INT);
            $st->execute();
            // 如果有字节点
            $st = $db->prepare('insert into  items (`fid`,`depth`,`t_left`,`t_right`,`user_id`,`name`,`rank`,`u_time`,`status`) VALUES (:fid,:depth,:left,:right,:userId,:name,:rank,now(),"enable")');
            $st->bindValue(':fid', intval($fathor['id']), PDO::PARAM_INT);
            $st->bindValue(':depth', intval($fathor['depth'] + 1), PDO::PARAM_INT);
            $st->bindValue(':userId', 1, PDO::PARAM_INT);
            $st->bindValue(':name', $_REQUEST['name'], PDO::PARAM_STR);
            $st->bindValue(':rank', $rank, PDO::PARAM_STR);
            if (isset($max_child_right)) {
                // 跟新所有上层父节点,给新插入的字节点腾出空间
                $st->bindValue(':left', intval($max_child_right + 1), PDO::PARAM_INT);
                $st->bindValue(':right', intval($max_child_right + 2), PDO::PARAM_INT);
            } else {
                // 跟新所有上层父节点,给新插入的字节点腾出空间
                $st->bindValue(':left', intval($fathor['t_right'] + 1), PDO::PARAM_INT);
                $st->bindValue(':right', intval($fathor['t_right'] + 2), PDO::PARAM_INT);
            }
            $status = $st->execute();
            if ($status) {
                $id = $db->lastInsertId();
            } else {
                $message = $db->errorInfo();
            }
        }
        Ajax::json($status, ['id'=>$id], $message);
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     * 为事项添加排序功能
     *  rankFrom = RankTo - 1
     *
     * request params:
     * name |meaning
     * ---|---
     * int $dragFrom
     * int $dragTo
     *
     *
     */
    public function rankPOST()
    {
        $message = "";
        /**
         * @var PDO $db
         */
        $db = $this->db;
        $st = $db->prepare('select `rank` from `items` WHERE `id` = :dragTo AND user_id = 1');
        $st->bindValue(':dragTo', intval($_REQUEST['dragTo']), PDO::PARAM_INT);
        $st->execute();
        $toRank = (int)$st->fetch(PDO::FETCH_COLUMN);
        // 防止排序好号码为 0
        $rank = $toRank > 0 ? $toRank - 1 : 0;
        $st = $db->prepare('update `items` set `rank` = :rank WHERE user_id = 1 AND id = :dragFrom');
        $st->bindValue(':dragFrom', intval($_REQUEST['dragFrom']), PDO::PARAM_INT);
        $st->bindValue(':rank', $rank, PDO::PARAM_INT);
        $status = $st->execute();
        Ajax::json($status, $rank, $message);
    }

    public function getItemNotesPOST()
    {
        $sql = 'select * from `notes` where `item_id` = :item_id ORDER BY `c_time` DESC';
        $params = [':item_id' => intval($_REQUEST['item_id'])];
        $result = Query::all($sql, $params);
        Ajax::json(true, $result, 'success');
    }

    public function itemDraftPOST()
    {
        /**
         * @var PDO $db
         */
        $db = $this->db;
        $st = $db->prepare('update `items` set `status` = "draft" WHERE id = :id');
        $st->bindValue(':id', intval($_REQUEST['id']), PDO::PARAM_INT);
        $updated = $st->execute();
        Ajax::json($updated);
    }

    public function parentDirGET(){
        $item = (new Items())->load($_REQUEST['id']);
        if(!$item) {
            return Ajax::json(0);
        }
        Ajax::json(1,['dir'=>$item->fid]);
    }
}