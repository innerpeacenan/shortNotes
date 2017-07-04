<?php
namespace n\models;

use \nxn\db\ActiveRecord;

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

    public function getColumns()
    {
        return static::$_columns;
    }

    public static function getPrimaryKey()
    {
        return static::$_primaryKey;
    }

}