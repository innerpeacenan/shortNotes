<?php

namespace n\modules\index\controllers;

use n\models\Items;
use nxn\debug\VarDumper;
use nxn\pipeline\PipeLine;
use nxn\web\Ajax;
use n\modules\account\controllers\AuthController;
use Log;

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
    public function __construct()
    {
        $this->pipelineTest();
    }

    protected function pipelineTest()
    {
        $pipeline = new PipeLine();
        $travelor = [];
        // 从log上看,后加入的被先执行了
        $pipeline->send($travelor)->through([function ($travelor, \Closure $next) {
            \Log::pipelineTest('middleWare1');
            return $next($travelor);
        }, function ($travelor, \Closure $next) {
            \Log::pipelineTest('middleWare2');
            return $next($travelor);
            //  代码中有是否为 closure的实例对象的检查
        }, function ($travelor, \Closure $next) {
            \Log::pipelineTest('middleware3');
            return $next($travelor);
        }])->then(function ($travelor) {
            \Log::pipelineTest(json_encode($travelor) . '  down');
        });
    }

    public function getItems()
    {
        $rulle = [
            'fid' => [
                'sometimes' => [],
                'int' => []
            ],
            'status' => [
                'inArray' => [
                    [
                        Items::STATUS_ENABLE,
                    ]
                ],
            ],
        ];

        $desciption = [
            'fid' => '父级事项ID',
            'status' => '事项状态'
        ];

        $this->validate($rulle, $desciption);

        $fid = empty($_REQUEST['fid']) ? 0 : intval($_REQUEST['fid']);
        $status = $_REQUEST['status'];
        $status = empty($status) ? [Items::STATUS_ENABLE] : (is_string($status) ? [$status] : $status);
        $userId = $_SESSION['user_id'];
        $result = Items::getItems($fid, $userId, $status);
        Ajax::json(true, $result, "success");
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 保存事项
     * request params:
     * [ 'id' => 'item id', 'name' => 'item name', 'fid' => 'parent id' ]
     */
    public function putItem()
    {
        $_REQUEST['fid'] = $_REQUEST['fid'] ?? 0;
        $item = new Items();
        $item->setAttributes($_REQUEST);
        $status = $item->save(false);
        Ajax::json($status, ['id' => $item->id]);
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
        $status = Items::deleteItem($_REQUEST['id'], $_SESSION['user_id']);
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
        $rank = Items::rank($_REQUEST['dragFrom'], $_REQUEST['dragTo'], $_REQUEST['rank']);
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
            Ajax::json(1, ['dir' => $item->fid]);
        }
    }

    public function putToggleVisibleRange()
    {
        $id = $_REQUEST['id'];
        $visibleRange = Items::toggleVisibleRange($id, $_SESSION['user_id']);
        Ajax::json(1, ['visible_range' => $visibleRange]);
    }
}