<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body onload="load_users()">
<div id="wrapper">
<h1>Auto GPT</h1>
<?php
    require "views/header.php";
?>
    <div id="user_wrapp">
        <button class="btn btn-primary" onClick="create_user_form()" id="new_user_button">Add new user</button>
    </div>
    
    
</div>
</body>
<script>
    
    let editing_user=[];
    let name_editing_user;
    let wrapper=document.getElementById("user_wrapp");
    if(!localStorage.actual_view){
        localStorage.setItem("actual_view","users");
        console.log(localStorage)
    }else{
        localStorage.actual_view="users";
        console.log(document.getElementById("clients_menu"))
        document.getElementById("clients_menu").setAttribute("class",'selected')
    }
    function load_users(){
        $.ajax({
            type : "POST", 
            url  : "back.php",  
            data : { getUsers:true},
            success: function(res){ 
                let answer=JSON.parse(res);
                if(res.not_users!="Users are empty"){
                var newTable = document.createElement("ul");
                newTable.id="list_of_users";
               
                newTable.innerHTML="<tr><th>Id</th><th>Name</th><th>Actions</th></tr>";                
                answer.forEach(element => {
                    var newRow = document.createElement("li");
                    newRow.setAttribute("class","user");
                    
                    var link = document.createElement("a");
                    var name=document.createTextNode(element.display_name);
                    var newColumn2 = document.createElement("div");
                    newColumn2.setAttribute("id","name_user_"+element.ID);
                    var id=document.createTextNode(element.ID);
                    link.appendChild(name);
                    link.setAttribute("href", "index.php"+"?user_id="+element.ID);
                    newColumn2.appendChild(link);
                    newRow.appendChild(newColumn2);
                    newTable.appendChild(newRow);
                    wrapper.appendChild(newTable);
                    let actionButton = "<div id='action_button_"+element.ID+"'><button  class='button_link' onClick=\"edit_user('" + element.ID + "','"+element.display_name+"')\">Edit</button><button  class='button_link' onClick=\"delete_user('" + element.ID + "','"+element.display_name+"')\">Delete</button></div>";

                    newRow.innerHTML+=actionButton;
                });
                
                }
               
            }
        });
    }
    function confirm_editing(user_id){
        console.log(user_id+name)
        let new_name_input=document.getElementById("new_name").value;
        $.ajax({
                type : "POST",  
                url  : "back.php",  
                data : { edit_user:user_id,new_name:new_name_input},
                success: function(res){  
                    location.reload();
                }
            });
    }
    function confirm_delete(user_id){
        $.ajax({
                type : "POST",  
                url  : "back.php",  
                data : { delete_user:user_id},
                success: function(res){  
                    location.reload();
                }
            });
    }
    function cancel_editing(user_id,name){
        document.getElementById("action_button_"+user_id).innerHTML="<button class='button_link' onClick=\"edit_user('" +user_id + "','"+name+"')\">Edit</button><button class='button_link' onClick=\"delete_user('" +user_id + "','"+name+"')\">Delete</button>";
        console.log(name)
        document.getElementById("name_user_"+user_id).innerHTML="<a href='index.php?user_id="+user_id+"'>"+name+"</a>";
    }
    function delete_user(user_id,value){
        console.log(name_editing_user);
        if(editing_user.length>0){
            document.getElementById("name_user_"+editing_user[0]).innerHTML="<a href='index.php?user_id="+editing_user[0]+"'>"+name_editing_user+"</a>";
            document.getElementById("action_button_"+editing_user[0]).innerHTML="<button  class='button_link' onClick=\"edit_user('" +editing_user[0]+ "','"+name_editing_user+"')\">Edit</button><button  class='button_link' onClick=\"delete_user('" +editing_user[0]+ "','"+name_editing_user+"')\">Delete</button>";
            editing_user=[];
            name_editing_user="";
        }
        document.getElementById("action_button_"+user_id).innerHTML="<button onClick=\"cancel_editing('" +user_id + "','"+value+"')\">Cancel</button><button onClick=\"confirm_delete('" +user_id +"')\">Confirm</button>";
        editing_user.push(user_id);
        name_editing_user=value;
        
    }
    function edit_user(user_id,value){
        console.log(name_editing_user);
        if(editing_user.length>0){
            document.getElementById("name_user_"+editing_user[0]).innerHTML="<a href='index.php?user_id="+editing_user[0]+"'>"+name_editing_user+"</a>";
            document.getElementById("action_button_"+editing_user[0]).innerHTML="<button  class='button_link' onClick=\"edit_user('" +editing_user[0]+ "','"+name_editing_user+"')\">Edit</button><button  class='button_link' onClick=\"delete_user('" +editing_user[0]+ "','"+name_editing_user+"')\">Delete</button>";
            editing_user=[];
            name_editing_user="";
        }
        document.getElementById("action_button_"+user_id).innerHTML="<button onClick=\"cancel_editing('" +user_id + "','"+value+"')\">Cancel</button><button onClick=\"confirm_editing('" +user_id + "','"+value+"')\">Confirm</button>";
        editing_user.push(user_id);
        name_editing_user=value;
        document.getElementById("name_user_"+user_id).innerHTML="<input type='text' id='new_name' value="+value+">";
    }
    function create_user_form(){
        var newDiv = document.createElement("div");
        newDiv.setAttribute("id","new_user_form");
        document.getElementById("new_user_button").setAttribute('disabled',true);
        newDiv.innerHTML="<h3>New user Form</h3><p><label for='name'>Name:</label><input type='text' id='name'/></p><button class='btn btn-info' onClick='add_user()'>Create user</button>";
        wrapper.appendChild(newDiv);
    }
    function add_user(){
        if(document.getElementById("name").value!=""){
            let name_user=document.getElementById("name").value;
            $.ajax({
                type : "POST",  
                url  : "back.php",  
                data : { new_user:name_user},
                success: function(res){  
                    document.getElementById("new_user_form").remove();   
                    var newMessage = document.createElement("p");
                    newMessage.setAttribute("id","message");
                    var text=document.createTextNode("User inserted");
                    newMessage.appendChild(text);
                    wrapper.appendChild(newMessage)
                    document.getElementById("new_user_button").removeAttribute('disabled');
                    location.reload();
                }
            });
        }else{

        }
    }
</script>
</html>