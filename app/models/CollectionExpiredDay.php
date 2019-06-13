<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;

class CollectionExpiredDay extends ActiveRecord
{
    const SATUS_ENABLE = 10;//暂停
    const STATUS_DISABLE = 20;//恢复

    // 实现了暂停机制
    public static function getByCollectionIds(array $collectionIds, $date)
    {
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id IN (:collection_id)  AND `date` <= :date order by `collection_id` asc, `date` asc,`id` asc ';//按照id排序,则最后插入的一条是最新的状态(排序是实现上的关键点)
        $results = Query::all($sql, [
            ':collection_id' => $collectionIds,
            ':date' => $date,
        ]);
        return $results;
    }

    /**
     * @param $collectionId
     * @param $date
     * @return int
     * 完善了暂停时间的计算逻辑
     */
    public static function getStoppedDays($collectionId, $date)
    {
        // 按照日期和状态一次进行排序,保证同一天内的暂停和恢复对应上.
        $sql = 'SELECT * FROM `collection_expired_day` WHERE collection_id = :collection_id  and `date` <= :date ORDER BY `date` asc, `status` asc';
        $days = Query::all($sql, [
            ':collection_id' => $collectionId,
            ':date' => $date,
        ]);
        $suspendStartDay = null;
        $suspendDays = 0;
        foreach ($days as $day) {
            if (self::SATUS_ENABLE == $day['status']) {
                if (!isset($suspendStartDay)) {
                    $suspendStartDay = $day;//找到第一个暂停时间
                }
            } else {
                if (empty($suspendStartDay)) {
                    continue;//如果在找到恢复记录,但是却没有与之对应的过期设计记录,则忽略恢复记录继续查找(避免同一天多条恢复记录的计算错误)
                }
                $suspendDays += (int)((strtotime($day['date']) - strtotime($suspendStartDay['date'])) / (24 * 3600));
                $suspendStartDay = null;
            }
        }
        //如果一直到遍历完成,距离给定日期的最后一次暂态设置仍然没有回复,则将其距离当前日期的时间作为最后一段暂停时间
        if (isset($suspendStartDay)) {
            $suspendDays += (int)((strtotime($date['date']) - strtotime($suspendStartDay['date'])) / (24 * 3600));
        }
        return $suspendDays;
    }
}