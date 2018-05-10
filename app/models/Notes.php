<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

/**
 * Class Notes
 * @package N\models
 * User: xiaoning nan
 * Date: ${YEAR}-${MONTH}-{$DAY}
 * Time: xx:xx
 * Description: description
 * N\models\Notes
 */
class Notes extends ActiveRecord
{
    /**
     * @var array 首次测试了 map 方法,起作用了
     */
    public static $map = [
        'update' => ['itemId' => 'item_id'],
    ];

    public static function tableName()
    {
        return 'notes';
    }

    public static function notesByItem($userId, $item_id, $opt = [], $tagIds = [])
    {
        if (!isset($opt['offset'])) {
            $opt['offset'] = 0;
        }
        if (!isset($opt['limit'])) {
            $opt['limit'] = 10;
        }
        $tagfilter = [Tags::$defaultTags['todo']];
        $tagIds = array_unique(array_merge($tagIds, $tagfilter));
        $params = [
            ':id' => (int)$item_id,
            ':user_id' => (int)$userId,
            ':tagids' => $tagIds,
            // limit and offset should be int, this is important
            ':limit' => (int)$opt['limit'],
            ':offset' => (int)$opt['offset'],
        ];
        // 值选择没有tag的笔记和具有指定的tag的笔记
        // 没有tag的笔记怎么取呢?
        $sql = 'SELECT DISTINCT n.*
FROM `notes` AS n INNER JOIN `items` AS i ON n.`item_id` = i.`id`
  INNER JOIN `users` AS u ON i.user_id = u.id
  LEFT JOIN notes_tag_rel AS nt ON n.id = nt.note_id
WHERE i.id = :id AND i.user_id = :user_id AND ((nt.tag_id IS NULL) OR (nt.tag_id IN (:tagids)))
ORDER BY n.c_time DESC
LIMIT :limit OFFSET :offset';
        $notes = Query::all($sql, $params);
        return $notes;
    }

    public static function deleteNotes($item_id)
    {
        return Query::execute('DELETE FROM notes WHERE item_id = :item_id', [':item_id' => $item_id]);
    }

    public static function checkNoteBelongsToUser($noteId, $userId)
    {
        $params = [':note_id' => $noteId, ':user_id' => $userId];
        $sql = 'select `item_id` from notes where id = :note_id limit 1';

        $itemId = Query::scalar($sql, $params);
        if (empty($itemId)) {
            throw new \Exception('该笔记对应事项已经被删除', 402);
        }
        \Log::info('item_id' . $itemId);
        $sql = 'select `user_id` from `items` where `id` = :id';
        $params = [':id' => $itemId];
        $actualUserId = Query::scalar($sql, $params);
        return (int)$actualUserId === (int)$userId;
    }

}
