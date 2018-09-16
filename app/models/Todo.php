<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;


class Todo extends ActiveRecord
{
    public static $displayTypes = [
        self::DISPLAY_OPTIONS,
        self::display_check_box,
    ];

    public static $typeList = [
        self::TYPE_TODO
    ];

    const DISPLAY_OPTIONS = 10;
    const display_check_box = 20;
    const TYPE_TODO = 20; // 段代办的集合


    public static function getByCollectionIds($collectionIds)
    {
        $sql = 'SELECT * FROM `todo` WHERE `collection_id` IN  (:collection_id)';
        return Query::all($sql, [':collection_id' => $collectionIds,]);
    }
}