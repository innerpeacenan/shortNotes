<?php

namespace n\modules\index\controllers;

use n\models\Collection;
use n\models\CollectionChecked;
use n\models\CollectionExpiredDay;
use n\models\Items;
use n\models\Todo;
use n\models\TodoCheckInLog;
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
        if(empty($_REQUEST['fid'])){
            $_REQUEST['fid'] =  0;
        }
        $_REQUEST['visible_range'] = 10;
        $_REQUEST['u_time'] = date('Y-m-d H:i:s');
        $item = new Items();
        $item->setAttributes($_REQUEST);
        $status = $item->save(false);
        Ajax::json($status, ['id' => $item->id, 'rank' => $item->rank]);
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

    public function getTodoList()
    {
        $itemId = $_REQUEST['item_id'];

        $collections = Collection::getByItemId($itemId);
        $collections = array_column($collections, null, 'id');
        $ids = array_column($collections, 'id');
        if(!empty($ids)){
            $expiredList = CollectionExpiredDay::getByCollectionIds($ids);
            $expiredList = array_column($expiredList, null, 'collection_id');
            foreach ($expiredList as $expired) {
                $exp = Collection::load($expired['collection_id']);
                $exp->status = Collection::STATUS_DISABLE;
                $exp->save();
            }
            $checkedList = CollectionChecked::getManualChecked($ids);
            $checkedList = array_column($checkedList, null, 'collection_id');
            $collections = array_diff_key($collections, $expiredList, $checkedList);
            $ids = [];
            foreach ($collections as $id => $collection){
                $ids[] = $id;
                $collections[$id]['total_count'] = Collection::getTotalDaysCount($id);
                $collections[$id]['check_in_count'] = Collection::getCheckedIndayCount($id);
            }
        }

        if(!empty($collections)){
            // find todos
            $todoList = Todo::getByCollectionIds($ids);
            $todoIds = array_column($todoList, 'id');
            $todoList = array_column($todoList, null, 'id');
            // 今天签到过的统统移除
            $todoFilter = TodoCheckInLog::getBlackLists($todoIds);
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
        $todo = Todo::load($todoId);
        $todoId = TodoCheckInLog::todoDoneToday($todoId);
        CollectionChecked::autoDoneToday($todo->collection_id);
        Ajax::json(1, ['todo_id' => $todoId]);
    }

    public function postCollectionDoneToday(){
        $collectionId = $_REQUEST['collection_id'];
        $collectionId = CollectionChecked::doneToday($collectionId);
        Ajax::json(1, ['collection_id' => $collectionId]);
    }
}
