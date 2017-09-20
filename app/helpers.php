<?php
if (!function_exists('createNonceStr')){
    function createNonceStr($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $str.=time();
        return str_shuffle($str);
    }
}
if (!function_exists('setUserToken')){
    function setUserToken($key,$value)
    {
        $expiresAt = \Carbon\Carbon::now()->addMinutes(30);
        \Illuminate\Support\Facades\Cache::put($key,$value,$expiresAt);
    }
}
if (!function_exists('getUserToken')) {
    function getUserToken($key)
    {
        $uid = \Illuminate\Support\Facades\Cache::get($key);
        if (!isset($uid)){
            return false;
        }
        return $uid;
    }
}
if (!function_exists('setTeacherToken')){
    function setTeacherToken($key,$value)
    {
        $expiresAt = \Carbon\Carbon::now()->addMinutes(30);
        \Illuminate\Support\Facades\Cache::put($key,$value,$expiresAt);
    }
}
if (!function_exists('getTeacherToken')) {
    function getTeacherToken($key)
    {
        $uid = \Illuminate\Support\Facades\Cache::get($key);
        if (!isset($uid)){
            return false;
        }
        return $uid;
    }
}