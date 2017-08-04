<?php
namespace n\models;

use nxn\db\ActiveRecord;

class Items extends ActiveRecord
{
    public static function tableName()
    {
        return 'items';
    }

    /**
     * @param string | array $primaryKey
     * @return void
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



}