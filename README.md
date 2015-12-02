
# 短信验证码校验的实现
[TOC]
## 1. 根据手机号码发送验证码
### 关于haoservice
公司这边外包项目为了实现短信验证功能而向haoservice买的功能，其实就是一串url，在里面有几个参数。
http://apis.haoservice.com/sms/send?mobile{$phone}&tpl_id=2&tpl_value=%23code%23%3d{$code}%26%23company%23%3d{$company}&key={$key}

比较重要的是以下几个：
mobile 你所要发送验证码的号码
tpl_value 为你要发送的信息，注意，必须以
\#code#={$code}&#company#={$company}
形式去urlEncode,其中的{$code}是你要发送的验证码，{$company}为你的公司名,{$key}则是你向haoservice购买服务后提供给你的key值
直接访问即可发送验证码

验证码可以根据自己需要随意生成，电话号码就得通过用户输入，再通过表单提交或者ajax来实现
这里我用的是ajax来实现

```
$.ajax({url: "./send_code.php", data: {phone: $("#phone").val()}});
```
### 在session保存验证码
由于需要比对，所以肯定是需要有一个校验本的，至于是放在哪里，就是得根据实际情况来决定，由于现在情况是，验证码只能存活一段时间而已，且是用完即弃的玩意，最好还是用session来保存验证码

```
$lifetime=60;//保存1分钟
session_start();
setcookie(session_name(),session_id(),time()+$lifetime,"/");
$_SESSION["code"] = $time; //保存验证码
```
而在验证完毕后也是得尽快销毁
```
if ($code == $_GET["code"]) {
            unset($_SESSION['code']);
            echo 'successful';
        }
```

## 2. 倒计时再发送的按钮
不废话直接上代码
```
 $(".msgs").click(function (event) {
                var time = 30;
                var code = $(this);
                if (validCode) {
                    validCode = false;
                    code.addClass("msgs1");
                    var t = setInterval(function () {
                        time--;
                        code.html(time + "秒");
                        if (time == 0) {
                            clearInterval(t);
                            code.html("重新获取");
                            validCode = true;
                            code.removeClass("msgs1");
                        }
                    }, 1000);
                }
            });
```
msgs元素是获取验证码的按钮，利用setInterval来实现倒计时的功能，并利用css来改变它的可按与不可按
css如下
```
        .msgs {
            display: inline-block;
            width: 104px;
            color: #fff;
            font-size: 12px;
            border: 1px solid #0697DA;
            text-align: center;
            height: 30px;
            line-height: 30px;
            background: #0697DA;
            cursor: pointer;
        }

        .msgs1 {
            background: #E6E6E6;
            color: #818080;
            border: 1px solid #CCCCCC;
        }
```
## 3. 校对验证码
这边依旧是使用ajax来实现验证码的校对，因为不希望用户输错验证码后界面被完全刷新而得重新输入
```
 $("#check").click(function () {
                var code = $("#code").val();
                if (code == '') {
                    alert("验证码不为空");
                    return false;
                }
                else {
                    $.ajax({
                        url: "./check_code.php",
                        data: {code: $("#code").val()},
                        success: function (data) {
                            alert(data);
                            if (data == "successful") {
                                location.href = "./index.php";
                            }
                            else if(data == "fail"){
                                alert("验证码错误");
                            }
                        }
                    });
                }
            });
        })
```
check_code.php的代码如下
```
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
```
