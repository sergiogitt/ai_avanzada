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
        <textarea id="prompt1" oninput="inserting_into_db(1)" rows='20' cols='70'></textarea>
        <button onclick="send_ai(1)">Send To AI</button>
        <button onclick="new_interaction()">New iteration +</button>
        <p id="action1"></p>
    </div>
    
</body>
<script>
        let number_actual_block=1;
        let number_new_block=number_actual_block+1;
        let ids_prompts=[];
        let interation=0;
        let inputs=[];
        
        inputs[1]=true;
        function new_interaction(){
            let name_prompt="prompt"+number_actual_block;
            let name_last_div="block"+number_actual_block;
            let name_last_action="action"+number_actual_block;
            if(document.getElementById(name_prompt).value!=""){
                document.getElementById(name_last_action).innerHTML ="";     
                interation++;
                
                // create a new div element
                var newDiv = document.createElement("div");
                newDiv.setAttribute("id", "block"+number_new_block);

                var newTextarea = document.createElement("textarea");
                newTextarea.setAttribute("id", "prompt"+number_new_block);
                newTextarea.setAttribute("rows", "20");
                newTextarea.setAttribute("cols", "70");
                newTextarea.setAttribute("oninput",`inserting_into_db('${number_new_block}')`);
                
                // create a new button element
                var newButton = document.createElement("button");
                newButton.innerHTML = "Send to AI";
                newButton.setAttribute("onclick",`send_ai('${number_new_block}')`);
                
                var newIterationButton = document.createElement("button");
                newIterationButton.innerHTML = "New iteration +";
                newIterationButton.setAttribute("onclick",`new_interaction('${number_new_block}')`);

                var newP = document.createElement("p");
                newP.setAttribute("id", "action"+number_new_block);
                newP.appendChild(newTextarea);
                

                // append the button to the new div
                newDiv.appendChild(newTextarea);
                newDiv.appendChild(newButton);
                newDiv.appendChild(newIterationButton);
                newDiv.appendChild(newP);
                // add the new div before the selected div
                document.body.appendChild(newDiv);
                number_actual_block++;
                number_new_block++;
                inputs[number_actual_block]=true;

            }else{
                document.getElementById(name_last_action).innerHTML ="Please type something before adding other iteration";
            }
            
        }
        function send_ai(number){
            console.log(inputs);
            let prompt="prompt"+number;
            let action="action"+number;
            if(document.getElementById(prompt).value!=""){
                interation++;
                document.getElementById(prompt).value=""; 
                inputs[number]=false;
                document.getElementById(action).innerHTML ="Prompt inserted";

            }else{
                document.getElementById(action).innerHTML ="Please type something";
            }
            
        }
        function inserting_into_db(number) {
            console.log(inputs);
            let prompt="prompt"+number;
            let action="action"+number;
            var input = document.getElementById(prompt).value;    
           
            if(inputs[number]){
                $.ajax({
                        type : "POST",  
                        url  : "back.php",  
                        data : { prompt : input ,attempt:interation,customer_id:1,iterations:number,id_prompt:ids_prompts[1]},
                        success: function(res){  
                            if(interation==0){
                                ids_prompts[number]=res;
                            }
                            
                            console.log(res);
                        }
                    });
                inputs[number]=false;
            }else{
                let ini=0;
                if(number==1){
                    ini=1;
                }
                $.ajax({
                        type : "POST", 
                        url  : "back.php",  
                        data : { prompt : input,prompt_id:ids_prompts[1],customer_id:1,iterations:number,initial:ini},
                        success: function(res){ 
                           console.log(res)
                        }
                    });
            }
        }
    </script>
</html>