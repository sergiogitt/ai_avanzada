<?php
$section="clients";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>AutoGPT</h1>
    <div class="menu">
        <ul>
            <li>CLIENTS</li>
            <li>ADD FILES</li>
            <li>AUTOGPT</li>
        </ul>
    </div>
    <div class="content">
    <?php
    switch($section){
        case "clients":
        require "views/clients.php";
        break;
        case "files":
        require "views/files.php";
        break;
        case "autogpt":
        require "views/autogpt.php";
        break;
    }
    ?>
    </div>
</body>
</html>