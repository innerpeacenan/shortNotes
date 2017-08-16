<?php
namespace n\modules\index\controllers;

use n\models\Items;
use nxn\debug\VarDumper;
use nxn\StringHelper;
use nxn\web\Ajax;
use nxn\web\AuthController;
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
class ItemController extends AuthController
{
    public $db;

    /**
     * AjaxController constructor.
     */

    public function __construct()
    {
        $this->db = \N::createObject('db');
    }

    public function getItems()
    {
        $fid = isset($_REQUEST['fid']) ? intval($_REQUEST['fid']) : 0;
        $result = Items::getItems($fid);
        Ajax::json(true, $result, "success");
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 保存事项
     * @todo 无线积分类，将这一部分内容放到 model 之中
     * @todo 先实现功能，再考虑功能健全性,先信任前端，在考虑后端, 一切以用户为中心,后端围绕用户作全方位的验证
     * request params:
     * [ 'id' => 'item id', 'name' => 'item name', 'fid' => 'parent id' ]
     */
    public function putItems()
    {
        $_REQUEST['fid'] = $_REQUEST['fid'] ?? 0;
        $item = new Items();
        $item->setAttributes($_REQUEST);
        $status = $item->save(false);
        if ($status) {
            Ajax::json($status, ['id' => $item->id]);
        } else {
            Ajax::json($status, ['id' => $item->id]);
        }
    }


    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * id | items.id
     *
     *
     */
    public function deleteItem()
    {
        $status = Items::deleteItem($_REQUEST['id']);
        Ajax::json($status);
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
    public function putRank()
    {
        $rank = Items::rank($_REQUEST['dragFrom'], $_REQUEST['dragTo']);
        $status = false === $rank ? 0 : 1;
        Ajax::json($status, ['rank' => $rank]);
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 切换 item 的状态
     * request params:
     * name |meaning
     * ---|---
     *
     *
     *
     */
    public function putItemDraft()
    {
        $item = Items::load($_REQUEST['id']);
        $updated = $item->toggleStatus();
        Ajax::json($updated, ['status' => $item->status]);
    }

    /**
     * @access
     * @return  void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * id | items.id,父级 item 的 id 值
     *
     */
    public function getParentDir()
    {
        $item = Items::load($_REQUEST['id']);
        if (!$item) {
            Ajax::json(0);
        } else {
            //  Trying to get property of non-object
            Ajax::json(1, ['dir' => $item->fid]);
        }
    }
}