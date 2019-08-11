<?php

namespace app\modules\index\controllers;

use app\models\Collection;
use app\models\CollectionChecked;
use app\models\CollectionExpiredDay;
use app\models\Items;
use app\models\Todo;
use app\models\TodoCheckInLog;
use app\modules\account\controllers\AuthController;
use play\pipeline\PipeLine;
use play\web\Ajax;
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
     * @deprecated
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
        if (0 === $status) {
            $status = 1;
        }
        $data = [
            'id' => $item->id,
        ];
        if ($item->hasAttribute('rank')) {
            $data['rank'] = $item->rank;
        }
        Ajax::json($status, $data);
    }


    /**
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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
     * @deprecated
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

    /**
     * @deprecated
     */
    public function putToggleVisibleRange()
    {
        $id = $_REQUEST['id'];
        $visibleRange = Items::toggleVisibleRange($id, $_SESSION['user_id']);
        Ajax::json(1, ['visible_range' => $visibleRange]);
    }

    public function getTodoList()
    {
        $itemId = $_REQUEST['item_id'];
        $date = $_REQUEST['date'];//Y-m-d
        $collections = Collection::getByItemId($itemId);
        $collections = array_column($collections, null, 'id');
        $ids = array_column($collections, 'id');
        if (!empty($ids)) {
            // 暂时并未过滤掉过期的集合
            $expiredList = CollectionExpiredDay::getByCollectionIds($ids, $date);
            $expiredList = array_column($expiredList, null, 'collection_id');
            foreach ($expiredList as $collectionId => $expired) {
                //同一集合的按照时间排序的最后一条如果是恢复记录,则到给定日期不算过期
                if ($expired['status'] == CollectionExpiredDay::STATUS_DISABLE) {
                    unset($expiredList[$collectionId]);
                }
            }
            //获取既没有过期,也没有被签到的那部分集合
            $checkedList = CollectionChecked::getManualChecked($ids, $date);
            $checkedList = array_column($checkedList, null, 'collection_id');
            $collections = array_diff_key($collections, $expiredList, $checkedList);
            $ids = [];
            foreach ($collections as $id => $collection) {
                $ids[] = $id;
                // 对每个集合,计算总天数
                $collections[$id]['total_count'] = Collection::getTotalDaysCount($id, $date);
                // 获取每个集合的签到总天数
                $collections[$id]['check_in_count'] = Collection::getCheckedIndayCount($id, $date);
            }
        }

        if (!empty($collections)) {
            // find todos
            $todoList = Todo::getByCollectionIds($ids);
            $todoIds = array_column($todoList, 'id');
            $todoList = array_column($todoList, null, 'id');
            // 今天签到过的统统移除
            $todoFilter = TodoCheckInLog::getBlackLists($todoIds, $date);
            $todoFilter = array_column($todoFilter, null, 'todo_id');
            $todoList = array_diff_key($todoList, $todoFilter);
            // reformat json
            foreach ($todoList as $todo) {
                $cid = $todo['collection_id'];
                if (!isset($collections[$cid]['todo'])) {
                    $collections[$cid]['todo'] = [];
                }
                $collections[$cid]['todo'][] = $todo;
            }
        }
        $collections = array_values($collections);
        Ajax::json(1, $collections);
    }

    public function postTodoDoneToday()
    {
        // 数据权限检查
        $todoId = $_REQUEST['todo_id'];
        $date = $_REQUEST['date'];
        $todo = Todo::load($todoId);
        $todoId = TodoCheckInLog::todoDoneToday($todoId, $date);
        CollectionChecked::autoDoneToday($todo->collection_id, $date);
        Ajax::json(1, ['todo_id' => $todoId]);
    }

    public function postCollectionDoneToday()
    {
        $collectionId = $_REQUEST['collection_id'];
        $date = $_REQUEST['date'];
        $collectionId = CollectionChecked::doneToday($collectionId, $date);
        Ajax::json(1, ['collection_id' => $collectionId]);
    }
}