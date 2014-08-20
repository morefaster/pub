<?php
    $name=$_POST['user'];
    $phone=$_POST['phone'];
    $from=$_POST['from'];
    $message_form=$_POST['message'];
 	
 	$to = "head123455@yanex.ru";
    $subject = "Заявка!";
    if($message_form){
    	$message = "<br>User: " . $name . "<br>Mail: " . $phone . "<br>Message:<br>" . $message_form . "<br>From: " . $from;
    }
    else $message = "<br>User: " . $name . "<br>Phone: " . $phone;
    
    $headers = "Content-type: text/html; charset=UTF-8 \r\n";
    $headers .= "From: TechCenter\r\n";
    //Если форма была отправлена, то выводим ее содержимое на экран
    if (isset($_POST["user"])) { 

        if (!mail($to, $subject, $message, $headers)) {
            $errors[] = "Ошибка, сообщение не отправлено. Попробуйте позже.";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Спасибо</title>
    <link rel="stylesheet" href="css/style.css">

    <script>

        var count = 5;
        var Interval = setInterval(
            function() {
                if (count == 0) {
                    document.location = 'index.html';
                    clearInterval(Interval);
                } else {
                    count--;
                    document.getElementById('count').innerHTML = count;
                }
            }, 1000
        );

    </script>
</head>
<body class="thx-page">
	<img src="img/thx-logo.jpg" />
	<div class="center">
		<h1 class="thx-title">Спасибо!</h1>
		<p class="thx-subtitle"><span>Наш менеджер свяжется с вами в ближайшее время</span></p>	
	
	</div>
</body>
</html>