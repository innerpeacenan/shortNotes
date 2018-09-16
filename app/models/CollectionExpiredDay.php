<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

class CollectionExpiredDay extends ActiveRecord
{
    const SATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    public static function getByCollectionIds(array $collectionIds)
    {
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id IN (:collection_id) AND `status` = :status AND `date` = :date';
        $results = Query::all($sql, [
            ':collection_id' => $collectionIds,
            ':status' => self::SATUS_ENABLE,
            ':date' => date('Y-m-d'),
        ]);
        return $results;
    }

    public static function getStoppedDays($collectionId){
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id = :collection_id AND `status` = :status ORDER BY `date` DESC ';
        $days = Query::all($sql, [
            ':collection_id' => $collectionId,
            ':status' => self::STATUS_DISABLE,
        ]);
        $total = 0;
        $prevDay = reset($days);
        foreach ($days as $day){
            $total += (strtotime($day) - strtotime($prevDay))/(24 * 3600);
            $prevDay = $day;
        }
        // @todo 加上免签的天数
        return $total;
    }
}