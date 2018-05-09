<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

/**
 * Class Tag
 * @package n\models
 * User: xiaoning nan
 * Description: description
 * tag 一旦创建,只能设置无效,不能删除,这是为了保证关系数据的完整新
 */
class Tag extends ActiveRecord
{
    const SATUS_ENABLE = 10;
    const STATUS_DISABLE = 20;
    private static $defaultTags = [
        'todo' => 1,
        'done' => 2
    ];

    public function getTags($userId)
    {
        $tagSql = 'select id,name from tags where user_id = :user_id';
        $results = Query::all($tagSql, $userId);
        return $results;
    }

    public function addNoteTag($noteId, $tagId, $userId)
    {
        $params = [':note_id' => $noteId, ':tag_id' => $tagId, ':use_id' => $userId];
        // 检查tag 是否属于user,
        $sql = 'select * from `tag_user_rel` WHere `tag_id` = :tag_id AND `user_id` = :user_id';
        if (!Query::one($sql, $params)) {
            throw  new \Exception('该tag不属于该用户', '402');
        }
        // 检查 notes_id 是否属于该用户
        if (Notes::checkNoteBelongsToUser($noteId, $userId)) {
            throw new \Exception('该笔记不属于该用户', 402);
        };
        // 检查tag是否已经添加过
        $sql = 'select * from `notes_tag_rel` where `note_id` = : note_id and `tag_id` = :tag_id ';
        if (Query::one($sql, $params)) {
            // 添加过就直接返回
            return 1;
        }
        $sql = 'insert into `notes_tag_rel` (`note_id`, `tag_id`) VALUES (:note_id, :tag_id)';
        $result = Query::execute($sql, $params);
        return $result;
    }

    public function removeNoteTag($noteId, $tagId, $userId)
    {
        // 检查note是否处于该用户
        if (Notes::checkNoteBelongsToUser($noteId, $userId)) {
            throw new \Exception('该笔记不属于该用户', 402);
        };
        $params = [':note_id' => $noteId, ':tag_id' => $tagId, ':use_id' => $userId];
        $sql = 'delete from `notes_tag_rel` where `note_id` = :note_id and `tag_id` = :tag_id';

        $result = Query::execute($sql, $params);
        return $result;
    }

    public function disableTag($tagId, $userId)
    {
        $params = [':tag_id' => $tagId, ':use_id' => $userId];
        // 检查tag 是否属于user,
        $sql = 'select * from `tag_user_rel` WHere `tag_id` = :tag_id AND `user_id` = :user_id';
        if (!Query::one($sql, $params)) {
            throw  new \Exception('该tag不属于该用户', '402');
        }
        $sql = 'update `tag` set `tag_status` = :tag_status WHERE `id` = :id';
        $params = [':tag_status' => self::STATUS_DISABLE, ':id' => $tagId];
        return Query::execute($sql, $params);
    }

    public function toggleTodoAndDone($noteId, $userId, $tagId)
    {
        if (!in_array($tagId, self::$defaultTags)) {
            throw new \Exception('tag_id should be either todo or done!, shoule in:'
                . json_encode(self::$defaultTags));
        }
        $params = [':note_id' => $noteId, ':use_id' => $userId, ':tag_id' => $tagId];
        $sql = 'select `tag_id` from `notes_tag_rel` where `note_id` = :note_id AND `tag_id` = :tag_id';
        $exists = Query::one($sql, $params);
        if (empty($exists)) {
            // insert
            $sql = 'insert into `notes_tag_rel` (`note_id`, `tag_id`) VALUES (:note_id, :tag_id)';
            $result = Query::execute($sql, $params);
            return $result;
        } else {
            $sql = 'update `notes_tag_rel` set `tag_id` = :tag_id where `note_id` = :note_id';
            $result = Query::execute($sql, $params);
            return $result;
        }
    }
}