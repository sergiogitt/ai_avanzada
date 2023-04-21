<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body onload="load_users()">
    <button class="btn btn-primary" onClick="create_user_form()" id="new_user_button">Add new user</button>
</body>
<script>

    function load_users(){
        $.ajax({
            type : "POST", 
            url  : "back.php",  
            data : { getUsers:true},
            success: function(res){ 
                let answer=JSON.parse(res);
                if(res.not_users!="Users are empty"){
                
                var newTable = document.createElement("table");
                newTable.setAttribute("border", 1);
                newTable.setAttribute("id", "table_users");
                document.body.appendChild(newTable);
                newTable.innerHTML="<tr><th>Id</th><th>Name</th></tr>";                
                answer.forEach(element => {
                    var newRow = document.createElement("tr");
                    var newColumn1 = document.createElement("td");
                    var link = document.createElement("a");
                    var name=document.createTextNode(element.display_name);
                    var newColumn2 = document.createElement("td");
                    var id=document.createTextNode(element.ID);
                    link.appendChild(name);
                    link.setAttribute("href", "index.php"+"?user_id="+element.ID);
                    newColumn1.appendChild(id);
                    newColumn2.appendChild(link);
                    newRow.appendChild(newColumn1);
                    newRow.appendChild(newColumn2);
                    newTable.appendChild(newRow);
                    console.log(element)
                    
                });
                
                }else{

                }
               
            }
        });
    }
    function create_user_form(){
        var newDiv = document.createElement("div");
        newDiv.setAttribute("id","new_user_form");
        document.getElementById("new_user_button").setAttribute('disabled',true);
        newDiv.innerHTML="<h3>New user Form</h3><p><label for='name'>Name:</label><input type='text' id='name'/></p><button class='btn btn-info' onClick='add_user()'>Create user</button>";
        document.body.appendChild(newDiv);
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
                    document.body.appendChild(newMessage)
                    document.getElementById("new_user_button").removeAttribute('disabled');
                    location.reload();
                }
            });
        }else{

        }
    }
</script>
</html>