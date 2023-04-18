<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body>
    <div id="block1">
        <textarea id="prompt1" ></textarea>
        <button onclick="send_ai(1)">Send To AI</button>
        <button onclick="new_interaction(1)">New iteration +</button>
        <p id="action1"></p>
    </div>
    <script>
        let number_actual_block=1;
        let number_new_block=number_actual_block+1;
        let first_input=true;
        let id_prompt=null;
        let interation=0;
        function new_interaction(){
            let name_prompt="prompt"+number_actual_block;
            let name_last_div="block"+number_actual_block;
            let name_last_action="action"+number_actual_block;
            if(document.getElementById(name_prompt).value!=""){
                document.getElementById(name_last_action).innerHTML ="";
            
                interation++;
                first_input=true;
                
               

                // create a new div element
                var newDiv = document.createElement("div");
                newDiv.setAttribute("id", "block"+number_new_block);
                var newTextarea = document.createElement("textarea");
                newTextarea.setAttribute("id", "prompt"+number_new_block);
                
                
                // create a new button element
                var newButton = document.createElement("button");
                newButton.innerHTML = "Send to AI";
                
                newButton.setAttribute("onclick",`send_ai('${number_new_block}')`);
                var newIterationButton = document.createElement("button");
                newIterationButton.innerHTML = "New iteration +";
                var newP = document.createElement("p");
                newP.setAttribute("id", "action"+number_new_block);
                newP.appendChild(newTextarea);
                newIterationButton.setAttribute("onclick",`new_interaction('${number_new_block}')`);

                // append the button to the new div
                newDiv.appendChild(newTextarea);
                newDiv.appendChild(newButton);
                newDiv.appendChild(newIterationButton);
                newDiv.appendChild(newP);
                // add the new div before the selected div
                document.body.appendChild(newDiv);
                number_actual_block++;
                number_new_block++;

            }else{
                document.getElementById(name_last_action).innerHTML ="Please type something before adding other iteration";
            }
            
        }
        function send_ai(number){
            let prompt="prompt"+number;
            let action="action"+number;
            if(document.getElementById(prompt).value!=""){
                interation++;
                document.getElementById(prompt).value=""; 
                first_input=true;
                document.getElementById(action).innerHTML ="Prompt inserted";

            }else{
                document.getElementById(action).innerHTML ="Please type something";
            }
            
        }
        function test(number) {
            let prompt="prompt"+number;
            let action="action"+number;
            var input = document.getElementById(prompt).value;          
            if(first_input){
                $.ajax({
                        type : "POST",  
                        url  : "back.php",  
                        data : { prompt : input ,attempt:true,customer_id:1,iterations:interation},
                        success: function(res){  
                            id_prompt=res;
                        }
                    });
                first_input=false;
            }else{
                $.ajax({
                        type : "POST", 
                        url  : "back.php",  
                        data : { prompt : input,prompt_id:id_prompt,customer_id:1,iterations:interation},
                        success: function(res){ 
                           
                        }
                    });
            }
        }
    </script>
</body>
</html>