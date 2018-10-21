<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;


class Image extends ActiveRecord
{
    public static function findNoteImages($noteId){
        $params = [':note_id' => $noteId];
        $sql = 'select * from image where note_id = :note_id';
        return Query::all($sql, $params);
    }
}