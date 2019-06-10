<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;


class TodoCheckInLog extends ActiveRecord
{

    public static function todoDoneToday($todoId, $date)
    {
        $todo = new self();
        $todo->setAttributes([
            'date' => $date,
            'todo_id' => $todoId,
        ]);
        $todo->save();
        return $todo->id;
    }

    public static function getBlackLists(array $todoIds, $date)
    {
        $sql = 'SELECT * FROM `todo_check_in_log` WHERE `todo_id` IN (:todoIds) AND `date` = :date';
        return Query::all($sql, [':todoIds' => $todoIds, ':date' => $date]);
    }

}