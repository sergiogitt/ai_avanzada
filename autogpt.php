<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body onload="get_actual_data_user(2)">
<div id="wrapper">
<h1>Auto GPT</h1>
<?php
    require "views/header.php";
?>

<div id="user_wrapp">
   
    <div id="blocks_container" >
        <div id="iteration_bar">
            <div id="iterations">
                <button class="selected iteration_button shadow " id="iteration1" onclick="load_iteration_data(1)">ITERATION 1</button>
            </div>
            
            <button type="button" class="iteration_button shadow" onclick="create_new_iteration_button(null,true)">+</button>
        </div>
        <div id="chat">
            <div id="input_area">
                <div id="action_buttons">
                    <button type="submit" class="button_link" onclick="send_ai(1):submit()">RUN</button>
                    <button type="button" class="button_link" onclick="delete_interaction()">DELETE</button>
                </div>
                <textarea id="prompt1" oninput="inserting_into_db(1)"  rows='20' cols='70'></textarea>
                <p id="error_message"></p>
            </div>
            <div id="output">
                <div>
                    <div class="spaced_inputs">
                        <label for="model">MODEL SELECTION</label>
                        <div><input type="text" id="model"></div>
                    </div>
                    <div class="spaced_inputs">
                        <label for="memmory">INCLUDE FILES AS MEMORY</label>
                        <div><input type="checkbox" id="memmory"></div>
                    </div>
                    <div class="spaced_inputs">
                        <label for="changes">SUGGEST CHANGES</label>
                        <div> <input type="checkbox" id="changes"></div>
                    </div>
                </div>
                <div id="response">
                    <h3>ITERATION OUTPUT</h3>
                    <p id="response_ai"></p>
                    <div id="loading"></div>
                </div>
                
            </div>
            <!--<form action="back.php" method="post" enctype="multipart/form-data">
                <div id="block1" >
                    <textarea id="prompt1" oninput="inserting_into_db(1)"  rows='20' cols='70'></textarea>
                    <div class="buttons">
                        <button type="submit" class="btn btn-primary" onclick="send_ai(1):submit()">Send To AI</button>
                        <button type="button" class="btn btn-primary" onclick="new_interaction()">New iteration +</button>
                        <input type="file" id="file1" name="file"></input>
                    </div>
                
                    <p id="action1"></p>
                </div>
            </form>-->
        </div>
        
    </div>

    
    
</div>
</body>
<script src="blackbox.js">    
</script>
<script>
     if(!localStorage.actual_view){
        localStorage.setItem("actual_view","autogpt");
        console.log(localStorage)
    }else{
        localStorage.actual_view="users";
        console.log(document.getElementById("autogpt_menu"))
        document.getElementById("autogpt_menu").setAttribute("class",'selected')
    }
</script>
</html>