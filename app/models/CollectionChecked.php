<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;


class CollectionChecked extends ActiveRecord
{
    const TYPE_MANUAL = 10; // 手动签到

    const TYPE_AUTO = 20; // 自动签到

    public static function getBlackLists(array $collectionIds)
    {
        $sql = 'SELECT * FROM `collection_checked` WHERE collection_id IN (:collection_id) AND `date` = :date';
        $results = Query::all($sql, [':collection_id' => $collectionIds, ':date' => date('Y-m-d')]);
        return $results;
    }

    public static function getManualChecked(array $collectionIds)
    {
        $sql = 'SELECT * FROM `collection_checked` WHERE collection_id IN (:collection_id) AND `date` = :date AND `type` = :manual_checked';
        $results = Query::all($sql, [
            ':collection_id' => $collectionIds,
            ':date' => date('Y-m-d'),
            ':manual_checked' => self::TYPE_MANUAL
        ]);
        return $results;
    }

    public static function doneToday($collectionId)
    {
        $result = self::getBlackLists([$collectionId]);
        $attributes = [
            'date' => date('Y-m-d'),
            'collection_id' => $collectionId,
            'type' => self::TYPE_MANUAL,
        ];
        $todo = new self();
        if (!empty($result)) {
            $attributes['id'] = $result[0]['id'];
        }
        $todo->setAttributes($attributes);
        $todo->save();
        return $todo->id;
    }

    public static function autoDoneToday($collectionId)
    {
        $result = self::getBlackLists([$collectionId]);
        if (empty($result)) {
            $todo = new self();
            $todo->setAttributes([
                'date' => date('Y-m-d'),
                'collection_id' => $collectionId,
                'type' => self::TYPE_AUTO,
            ]);
            $todo->save();
        }
    }

    public static function getTotalDaysCount($collectionId)
    {
        $sql = 'SELECT DISTINCT `date` FROM `collection_checked` WHERE collection_id = :collection_id';
        $results = Query::all($sql, [
            ':collection_id' => $collectionId,
        ]);
        return count($results);
    }

}