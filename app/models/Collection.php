<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

//  天数统计,总天数(需要签到的总天数) = 当前日期 - 集合创建时间 - 集合间隔天数综合 - 免签到天数;
//  已经签到的总天数(手动签到次数 + 自动签到次数)

// 在到期时间的时间间隔范围内的每一天都加入到 checked_in_date,这样到这一天的时候就不会显示了
class Collection extends ActiveRecord
{
    // 新增的状态以3为地精单元
    const SATUS_ENABLE = 10;

    const STATUS_DISABLE = 20;

    const TYPE_TODO = 20;

    public static function getByItemId($itemsId)
    {
        $sql = 'SELECT * FROM `collection` WHERE `item_id` = :items_id AND `status` = :status';
        return Query::all($sql, [':items_id' => $itemsId, ':status' => self::SATUS_ENABLE]);
    }

    // @todo 待测试
    public static function getTotalDaysCount($id)
    {
        $instance = self::load($id);
        $total = (strtotime(date('Y-m-d')) - strtotime($instance->create_date)) / (24 * 3600) + 1;
        $total = $total - CollectionExpiredDay::getStoppedDays($id);
        return $total;
    }

    // @todo 待测试
    public static function getCheckedIndayCount($id)
    {
        return CollectionChecked::getTotalDaysCount($id);
    }



}