<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/14/17
 * Time: 10:19 AM
 */
namespace n\models;

use nxn\db\ActiveRecord;
use nxn\db\Query;

/**
 * Class User
 * @package models
 * User: xiaoning nan
 * Date: 2017-08-{14}
 * Time: xx:xx
 * Description: description
 */
class Users extends ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }

    public static function userByName($userName)
    {
        return Query::one('SELECT * FROM `users` WHERE name = :name', [':name' => $userName]);
    }

}