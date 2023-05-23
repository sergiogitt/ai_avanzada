<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div id="wrapper">
        <form action="back.php" method="post">
        <div id="login">
            <div>
                <label for="user">User:<span hidden id="user_empty">*</span></label>
                <input type="text" id="user" name="user">
                
            </div>
            <div>
                <label for="password">Password:<span id="password_empty" hidden>*</span></label>
                <input type="password" id="password" name="password">
            </div>
            <div id="btn_login">
                <button name="logUser">Login</button>
            </div>
            <div >
               <p hidden id="empty_field">Please,fill in the fields marked with *</p>
               <p hidden id="wrong_credentials">Wrong credentials</p>
            </div>
        </div>
    </form>
    
    </div>
</body>
<script src="blackbox_login_agpt.js"></script>
</html>