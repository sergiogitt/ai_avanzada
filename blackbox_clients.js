if(!sessionStorage.ID){
    window.location.href = "login_agpt.php"; // redirect to login page
}
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
           
                   
            answer.forEach(element => {
                var newRow = document.createElement("li");
                newRow.setAttribute("class","user");
                
                var link = document.createElement("div");
                var name=document.createTextNode(element.display_name);
                var newColumn2 = document.createElement("div");
                newColumn2.setAttribute("id","name_user_"+element.ID);
                var id=document.createTextNode(element.ID);
                link.appendChild(name);
               
               
                
                newColumn2.appendChild(link);
                newRow.appendChild(newColumn2);
                newTable.appendChild(newRow);
                if(sessionStorage.rol=="admin"){
                    link.setAttribute("onclick", `create_session('${element.ID}')`);
                    let actionButton = "<div id='action_button_"+element.ID+"'><button  class='button_link' onClick=\"edit_user('" + element.ID + "','"+element.display_name+"')\">Edit</button><button  class='button_link' onClick=\"delete_user('" + element.ID + "','"+element.display_name+"')\">Delete</button></div>";
                    newRow.innerHTML+=actionButton;
                }
                wrapper.appendChild(newTable);
            });
            
            }
           
        }
    });
}
function create_session(id){
    sessionStorage.setItem("auto_gpt_view_user",id);
    console.log(sessionStorage)
    window.location.href = 'autogpt.php';
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
    document.getElementById("action_button_"+user_id).innerHTML="<button class='button_link' onClick=\"cancel_editing('" +user_id + "','"+value+"')\">Cancel</button><button class='button_link' onClick=\"confirm_delete('" +user_id +"')\">Confirm</button>";
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
    document.getElementById("action_button_"+user_id).innerHTML="<button class='button_link' onClick=\"cancel_editing('" +user_id + "','"+value+"')\">Cancel</button><button class='button_link' onClick=\"confirm_editing('" +user_id + "','"+value+"')\">Confirm</button>";
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