<?php
namespace n\models;

use \nxn\db\ActiveRecord;
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
        $sql = 'SELECT * FROM `notes` WHERE `item_id` = :item_id ORDER BY `c_time` DESC LIMIT :limit OFFSET :offset';
        $params = [':item_id' => (int)$item_id, ':offset' => (int)$opt['offset'], ':limit' => (int)$opt['limit']];
        return Query::all($sql, $params);
    }

    public static function deleteNotes($item_id)
    {
        return Query::execute('DELETE FROM notes WHERE item_id = :item_id', [':item_id' => $item_id]);
    }

}