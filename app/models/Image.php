<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;


class Image extends ActiveRecord
{
    const SATUS_ENABLE = 10;

    const STATUS_DISABLE = 20;

    public static function findNoteImages($noteId, $withBase64 = false){
        $params = [':note_id' => (int)$noteId, ':status' => self::SATUS_ENABLE];
        if($withBase64){
            $sql = 'select *  from image where note_id = :note_id and `status` =  :status';
        }else{
            $sql = 'select id, note_id, `index`, `status` from image where note_id = :note_id and `status` =  :status';
        }
        return Query::all($sql, $params);
    }
}