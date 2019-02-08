<?php

namespace app\models;

use play\db\ActiveRecord;
use play\db\Query;

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

    public static function notesByItem($userId, $item_id, $opt = [], $tagIds = [1])
    {
        if (!isset($opt['offset'])) {
            $opt['offset'] = 0;
        }
        if (!isset($opt['limit'])) {
            $opt['limit'] = 10;
        }
        $params = [
            ':id' => (int)$item_id,
            ':user_id' => (int)$userId,
            ':tagids' => $tagIds,
            // limit and offset should be int, this is important
            ':limit' => (int)$opt['limit'],
            ':offset' => (int)$opt['offset'],
        ];

        // 值选择没有tag的笔记和具有指定的tag的笔记
        $sql = 'SELECT DISTINCT n.*
FROM `notes` AS n INNER JOIN `items` AS i ON n.`item_id` = i.`id`
  INNER JOIN `users` AS u ON i.user_id = u.id
  LEFT JOIN notes_tag_rel AS nt ON n.id = nt.note_id
WHERE i.id = :id AND i.user_id = :user_id AND ((nt.tag_id IS NULL) OR (nt.tag_id IN (:tagids)))
ORDER BY n.c_time DESC
LIMIT :limit OFFSET :offset';
        $notes = Query::all($sql, $params);
        // 历史原因,为前端初始化数据
        foreach ($notes as $i => $note) {
            $notes[$i]['pictures'] = [];
        }
        return $notes;
    }

    public static function deleteNotes($itemId)
    {
        return Query::execute('DELETE FROM notes WHERE item_id = :item_id', [':item_id' => $itemId]);
    }

    public function addTodoLog($itemId)
    {
        \Log::info('item_id ' . $itemId . ' has been deleted, so take an note');
        \Log::info('item_id ' . $itemId . ' will Test tomorrow');
    }

    public static function checkNoteBelongsToUser($noteId, $userId)
    {
        $params = [':note_id' => $noteId, ':user_id' => $userId];
        $sql = 'SELECT `item_id` FROM notes WHERE id = :note_id LIMIT 1';

        $itemId = Query::scalar($sql, $params);
        if (empty($itemId)) {
            throw new \Exception('该笔记对应事项已经被删除', 402);
        }
        \Log::info('item_id' . $itemId);
        $sql = 'SELECT `user_id` FROM `items` WHERE `id` = :id';
        $params = [':id' => $itemId];
        $actualUserId = Query::scalar($sql, $params);
        return (int)$actualUserId === (int)$userId;
    }

}
