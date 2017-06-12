<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/4/17
 * Time: 11:03 AM
 */
namespace nxn\web;

/**
 * Class Response
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-06-{04}
 * Time: xx:xx
 * Description: description
 */
class Response
{
   public static function redirect (string $url){
       // check if the hander has been sent before,otherwise an notice error will be throw!
       if(headers_sent()) return;
       header('location:'.$url);
   }

   public static function deleteCookie($cookieName){
       setcookie($cookieName,null);
   }
}

