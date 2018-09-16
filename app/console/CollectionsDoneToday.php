<?php

// todo 后期加入定时任务
use \n\models\{
    Collection, CollectionExpiredDay, CollectionChecked, Todo, TodoCheckInLog
};

$itemId = $_REQUEST['item_id'];

$collections = Collection::getByItemId($itemId);
$collections = array_column($collections, null, 'id');
$ids = array_column($collections, 'id');
$expiredList = CollectionExpiredDay::getByCollectionIds($ids);
$expiredList = array_column($expiredList, null, 'id');
foreach ($expiredList as $expired) {
    $exp = Collection::load($expired['id']);
    $exp->status = Collection::STATUS_DISABLE;
    $exp->save();
}


$checkedList = CollectionChecked::getBlackLists($ids);
$checkedList = array_column($checkedList, null, 'collection_id');
$collections = array_diff_key($collections, $expiredList, $checkedList);

// 还可能有未签到的
if (!empty($collections)) {
    // find todos
    $todoList = Todo::getByCollectionIds($ids);
    $todoIds = array_column($todoList, 'id');
    $todoList = array_column($todoList, null, 'id');
    // 今天签到过的todo
    $todoFilter = TodoCheckInLog::getBlackLists($todoIds);
    $todoFilter = array_column($todoFilter, null, 'todo_id');
    $todoList = array_intersect_key($todoList, $todoFilter);

    foreach ($todoList as $todo) {
        $cid = $todo['collection_id'];
        $collectionId = $collections[$cid]['id'];
        if (!isset($collections[$cid]['checked'])) {
            $cc = new CollectionChecked();
            $cc->setAttributes([
                'collection_id' => $collectionId,
                'date' => date('Y-m-d'),
            ]);
            $cc->save();
            $collections[$cid]['checked'] = true;
        }
    }
}