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

    public static function notesByItem($item_id, $opt = [])
    {
        if (!isset($opt['offset'])) $opt['offset'] = 0;
        if (!isset($opt['limit'])) $opt['limit'] = 10;
        // 这里暂时过滤加成的标签
        $tagfilter = [Tag::$defaultTags['done']];
        //@todo check if items belongs to user
        $sql = 'SELECT * FROM `notes` WHERE `item_id` = :item_id ORDER BY `c_time` DESC LIMIT :limit OFFSET :offset';
        $params = [':item_id' => (int)$item_id, ':offset' => (int)$opt['offset'], ':limit' => (int)$opt['limit']];
        $notes = Query::all($sql, $params);
        // 先保证功能能用起来
        return $notes;
        foreach ($notes as $key => &$v) {
            $params = [':note_id' => $v['id']];
            $sql = 'select r.`id`, t.`name` from `notes_tag_rel` as r INNER join `tag` as t on r.tag_id = t.id WHERE r.`note_id` = :note_id';
            $tags = Query::all($sql, $params);
            \Log::tags($tags);
            $v['tags'] = $tags;
            foreach ($v['tags'] as $tag) {
                if (in_array($tag['id'], $tagfilter)) {
                    unset($v);
                }
            }
        }
        return array_values($notes);
    }

    public static function deleteNotes($item_id)
    {
        return Query::execute('DELETE FROM notes WHERE item_id = :item_id', [':item_id' => $item_id]);
    }

    public static function checkNoteBelongsToUser($noteId, $userId)
    {
        $params = [':note_id' => $noteId, ':use_id' => $userId];
        $sql = 'select `item_id` from notes where note_id = :note_id limit 1';
        $itemId = Query::scalar($sql, $params);
        if (empty($itemId)) {
            throw new \Exception('该笔记对应事项已经被删除', 402);
        }
        \Log::info('item_id' . $itemId);
        $sql = 'select `user_id` from `items where id = :id `';
        $params = [':id' => $itemId];
        $actualUserId = Query::scalar($sql, $params);
        return (int)$actualUserId === (int)$userId;
    }

}
