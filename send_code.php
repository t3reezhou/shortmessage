<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/16
 * Time: 15:38
 */

/**
 * ����sessionʱ��
 */
$time = time();
$lifetime=60;//����1����
session_start();
setcookie(session_name(),session_id(),time()+$lifetime,"/");


$_SESSION["code"] = $time; //������֤��


$phone = $_GET["phone"];
$url = "http://apis.haoservice.com/sms/send?mobile={$phone}&tpl_id=2&tpl_value=%23code%23%3d{$time}%26%23company%23%3d%E4%B8%80%E7%BA%BF%E7%94%9F%E9%B8%A1&key=e973e75f2b994ec09e10bb0aeddfe1f2";
//ִ��curl��������֤��
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$json = curl_exec($ch);
$jsonarray = json_decode($json, true);
//���ط��ͽ��
echo $jsonarray['error_code'];
?>