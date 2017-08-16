<?php
namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

class Items extends ActiveRecord
{
    /**
     * 表示全局可见，及在任何一个目录下都可以看到
     */
    const SHOW_GLOBAL = 1;

    /**
     *  启用
     */

    const ENABLE = 2;

    /**
     * 放入草稿箱
     */
    const DRAFT = 3;

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

    public static function getItems($fid)
    {
        $sql = 'SELECT * FROM `items` WHERE (`status` = :show_global OR `fid` = :fid ) AND `user_id` = :user_id ORDER BY `status` ASC , `rank` DESC';
        $param = [':show_global' => self::SHOW_GLOBAL, ':fid' => $fid, ':user_id' => $_SESSION['user_id']];
        return Query::all($sql, $param);
    }

    public static function deleteItem($id)
    {
        $item = self::load($_REQUEST['id']);
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
        $this->status = Items::DRAFT;
        $updated = $this->save(false);
        return $updated;
    }

    public function toggleStatus()
    {
        $this->status = $this->status == self::DRAFT ? self::ENABLE : Items::DRAFT;
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
     *
     */
    public static function rank($from, $to)
    {
        $sql = 'SELECT `rank` FROM `items` WHERE `id` = :id AND user_id = :user_id';
        $params = [':id' => (int)$to, ':user_id' => (int)$_SESSION['user_id']];
        $toRank = Query::scalar($sql, $params);
        if (!$toRank) {
            throw new \Exception('item:' . $to . 'does not exists!');
        }

        $params[':id'] = $from;
        $fromRank = Query::scalar($sql, $params);
        if (!$fromRank) {
            throw new \Exception('item:' . $from . 'does not exists!');
        }
        if ((int)$fromRank > (int)$toRank) {
            // from.rank = to.rank - 1
            $rank = $toRank > 0 ? $toRank - 1 : 0;
        } else {
            // from.rank = to.rank + 1
            $rank = $toRank > 0 ? $toRank + 1 : 0;
        }
        // 防止排序好号码为 0
        $params = [':rank' => $rank, ':user_id' => (int)$_SESSION['user_id'], ':dragFrom' => (int)$_REQUEST['dragFrom']];
        $status = Query::execute('UPDATE `items` SET `rank` = :rank WHERE user_id = :user_id AND id = :dragFrom', $params);
        if ($status) {
            return $rank;
        } else {
            return false;
        }
    }

   public function beforeInsert()
   {
       $this->user_id = $_SESSION['user_id'];
       $prevId = Query::scalar('select id from items ORDER BY id DESC');
       $rank = 10 * ($prevId + 1);
       $this->rank = $rank;
   }
}