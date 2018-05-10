<?php

namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

class Items extends ActiveRecord
{

    //表示全局可见，及在任何一个目录下都可以看到
    const SHOW_GLOBAL = 20;
    const SHOW_LOCAL = 10;
    const STATUS_ENABLE = 10;
    // 暂存的状态
    const STATUS_STATING = 20;
    //归档
    const STATUS_DRAFT = 30;


    public static function tableName()
    {
        return 'items';
    }

    /**
     * @param string | array $primaryKey
     * @return void
     *
     */
    public function setPrimaryKey($primaryKey)
    {
        if (is_string($primaryKey)) {
            $primaryKey = [$primaryKey];
        } elseif (!is_array($primaryKey)) {
            throw new \Exception('primary key must be an array or string!');
        }
        $this->_primaryKey = $primaryKey;
    }

    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    /**
     * getItems.
     * @access
     * @param $fid
     * @return array
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Date: xxxx-xx-xx
     * Time: xx:xx
     * Description:
     *
     */
    public static function getItems($fid, $userId, array $status = [])
    {
        // 默认查出启用的状态
        $status = array_unique(array_merge([self::STATUS_ENABLE], $status));
        $sql = 'SELECT *
FROM `items`
WHERE `user_id` = :user_id AND `status` IN (:status) AND (`visible_range` = :show_global OR `fid` = :fid)
ORDER BY `visible_range` DESC, `rank` DESC';
        $params = [
            ':show_global' => self::SHOW_GLOBAL,
            ':fid' => (int)$fid,
            ':user_id' => (int)$userId,
            ':status' => $status,
        ];
        return Query::all($sql, $params);
    }

    public static function deleteItem($id, $userId)
    {
        $item = self::load($_REQUEST['id']);
        if ($item->user_id != $userId) {
            throw new \Exception('items 不属于该用户', 402);
        }
        $status = $item->delete();
        return $status;
    }

    public function afterDelete()
    {
        Notes::deleteNotes($this->id);
        parent::afterDelete();
    }

    public function draft()
    {
        $this->status = Items::STATUS_DRAFT;
        $updated = $this->save(false);
        return $updated;
    }

    public function toggleStatus()
    {
        $this->status = $this->status == self::STATUS_DRAFT ? self::STATUS_ENABLE : Items::STATUS_DRAFT;
        $updated = $this->save(false);
        return $updated;
    }

    /**
     * @access
     * @param int $from item.id
     * @param int $to item.id
     * @return int|false item.rank of the from item, false on update failure
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * @todo 这种单条的操作应该用 activeRecord更加合适
     */
    public static function rank($from, $to, $rank)
    {
        $sql = 'SELECT `rank` FROM `items` WHERE `id` = :id AND user_id = :user_id';
        $params = [':id' => (int)$to, ':user_id' => (int)$_SESSION['user_id']];
        $toRank = Query::scalar($sql, $params);
        if (!$toRank) {
            throw new \Exception('item:' . $to . 'does not exists!');
        }

        $params[':id'] = $from;
        $fromRank = Query::scalar($sql, $params);
        // 有时候rank 的值就是0
        if ((!$fromRank) && (!is_numeric($fromRank))) {
            throw new \Exception('item id:' . $from . ' does not exists!');
        }

        // 防止排序好号码为 0
        $params = [':rank' => (float)$rank, ':user_id' => (int)$_SESSION['user_id'], ':dragFrom' => (int)$_REQUEST['dragFrom']];
        $status = Query::execute('UPDATE `items` SET `rank` = :rank WHERE user_id = :user_id AND id = :dragFrom', $params);
        if ($status) {
            return $rank;
        } else {
            return false;
        }
    }

    public static function toggleVisibleRange($id, $userId)
    {
        $item = self::load($id);
        if (empty($item)) {
            throw new \Exception('items 无效', 402);
        }
        if ($item->user_id != $userId) {
            throw new \Exception('items 不属于该用户', 402);
        }
        $item->visible_range = $item->visible_range == self::SHOW_GLOBAL ? self::SHOW_LOCAL : self::SHOW_GLOBAL;
        $item->save();
        return $item->visible_range;
    }

    /**
     * beforeInsert.
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Date: 20180506
     * Time: xx:xx
     * Description:
     */
    public function beforeInsert()
    {
        $this->user_id = $_SESSION['user_id'];
        $sql = 'select rank from items where user_id = :user_id ORDER BY rank DESC';
        $maxRank = Query::scalar($sql, [':user_id' => $this->user_id]);
        $this->rank = $maxRank + 2;
    }


}