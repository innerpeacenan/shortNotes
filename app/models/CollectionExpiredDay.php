<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;

class CollectionExpiredDay extends ActiveRecord
{
    const SATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;

    public static function getByCollectionIds(array $collectionIds, $date)
    {
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id IN (:collection_id) AND `status` = :status AND `date` <= :date';
        $results = Query::all($sql, [
            ':collection_id' => $collectionIds,
            ':status' => self::SATUS_ENABLE,
            ':date' => $date,
        ]);
        return $results;
    }

    public static function getStoppedDays($collectionId){
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id = :collection_id AND `status` = :status ORDER BY `date`';
        $days = Query::all($sql, [
            ':collection_id' => $collectionId,
            ':status' => self::STATUS_DISABLE,
        ]);
        $total = 0;
        foreach ($days as $day){
            // 两个间隔日期的两头都算做停滞日期,因此数量为 日期差 + 1
            if(isset($prevDay)){
                $total += (strtotime($day['date']) - strtotime($prevDay['date']))/(24 * 3600) + 1;
            }
            $prevDay = $day;
        }
        // @todo 加上免签的天数
        return $total;
    }
}