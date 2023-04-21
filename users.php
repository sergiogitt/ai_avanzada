<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client management</title>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body onload="load_users()">
    
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
                    newColumn2.appendChild(id);
                    newRow.appendChild(newColumn2);
                    newRow.appendChild(link);
                    newTable.appendChild(newRow);
                    console.log(element)
                    
                });
                
                }else{

                }
               
            }
        });
    }
</script>
</html>