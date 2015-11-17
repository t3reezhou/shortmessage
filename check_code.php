<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/17
 * Time: 10:06
 */
session_start();
if (isset($_SESSION['code'])) {
    $code = $_SESSION['code'];
    if (isset($_GET["code"])) {
        if ($code == $_GET["code"]) {
            unset($_SESSION['code']);
//            Header("Location: ./index.php");
            echo 'successful';
        } else {
            echo"fails";
        }
    }
}else{
    echo '验证码失效';
}