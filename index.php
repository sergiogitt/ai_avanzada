<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body onload="get_actual_data_user(1)">
<div id="flex_container">
    <div id="blocks_container">
        <div id="block1">
            <textarea id="prompt1" oninput="inserting_into_db(1)"  rows='20' cols='70'></textarea>
            <button type="submit" class="btn btn-primary" onclick="send_ai(1)">Send To AI</button>
            <button type="submit" class="btn btn-primary" onclick="new_interaction()">New iteration +</button>
            <p id="action1"></p>
        </div>
    </div>
   
    <div id="response">
        <h3>AI response</h3>
        <p id="response_ai"></p>
        <div id="loading"></div>
    </div>
</div>
</body>
<script>
        let number_actual_block=1;
        let number_new_block=number_actual_block+1;
        let ids_prompts=[];
        let interation=0;
        let control_insertion_prompts=[];        
        control_insertion_prompts[1]=true;
        let ini=0;
        let getting_info=false;
        function get_actual_data_user(user_id){
            $.ajax({
                    type : "POST",  
                    url  : "back.php",  
                    data : { customer_id:user_id,getFirstData:true},
                    success: function(res){   
                        let answer=JSON.parse(res);
                        if(answer.not_found!="Not found that client"){
                            control_insertion_prompts[1]=false;
                            getting_info=true;
                            ids_prompts[1]=answer.PROMPT_ID;
                            document.getElementById("prompt1").value=answer.prompt;
                            $.ajax({
                            type : "POST",  
                            url  : "back.php",  
                            data : { customer_id:user_id,getSecondData:true,prompt_id:ids_prompts[1]},
                            success: function(res2){  
                                let data=JSON.parse(res2);
                                data.forEach(element => {
                                    create_new_prompts_inputs(true,element);
                                    //Adding new element to the array which contorls the insertions or updates of the prompts
                                    control_insertion_prompts[number_actual_block]=false;
                                });
                                
                                
                                }
                            });
                        }
                    
                    }
                });
            
        }
        function send_ai(number){
            //ID of elements
            console.log(number);
            
            let prompt1="prompt"+number;
            let action="action"+number;
            let buttons_to_disable=document.getElementsByTagName('button');
            
            if(document.getElementById(prompt1).value!=""){
                document.getElementById("loading").setAttribute('class',"spinner-border");
                //Control if there is something on the textarea
                document.getElementById(prompt1).setAttribute('disabled', true);
                for (let i = 0; i < buttons_to_disable.length; i++) {
                    buttons_to_disable[i].setAttribute('disabled', true);
                }
                const REACT_APP_OPENAI_API_KEY="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";
               // Define the data variable with prompt and API parameters
               const API_ENDPOINT = "https://api.openai.com/v1/chat/completions";

                // Parámetros de la solicitud
                let prompt =document.getElementById(prompt1).value ;
                console.log(prompt)
                const max_tokens = 5;
                const n = 1;

                // Objeto de opciones para la solicitud
                const options = {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer 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"
                },
                body: JSON.stringify({
                    
                    "model": "gpt-3.5-turbo",
                    "messages": [{"role": "user", "content":prompt}],
                    "temperature": 0.7
                })
                };

                // Hacer la solicitud a la API
                fetch(API_ENDPOINT, options)
                .then(response => response.json())
                .then(data =>{ 
                    let content=data.choices[0].message.content;
                    console.log(content)
                    var newP = document.getElementById("response_ai");
                    newP.innerHTML=content;
                    document.getElementById("loading").removeAttribute('class');
                    for (let i = 0; i < buttons_to_disable.length; i++) {
                        buttons_to_disable[i].removeAttribute('disabled');
                    }
                    document.getElementById(prompt1).removeAttribute('disabled');
                    document.getElementById("response").appendChild(newP);
                    let num=number;
                    num++;
                    
                    let next_prompt="prompt"+(num);

                    console.log("Next prompt"+next_prompt);
                    console.log("Next number"+num);
                    if(document.getElementById(next_prompt)==null){
                        create_new_prompts_inputs(false);
                        $.ajax({
                        type : "POST",  
                        url  : "back.php",  
                        data : { prompt : content ,attempt:interation,customer_id:1,iterations:num,id_prompt:ids_prompts[1]},
                        success: function(res){  
                            if(interation==0){
                                ids_prompts[number]=res;
                            }                      
                        }
                    });
                control_insertion_prompts[number]=false;
                    }
                    document.getElementById(next_prompt).value=document.getElementById(next_prompt).value+"\nAPI RESPONE: "+content;
                })
                .catch(error => console.error(error));

            }else{
                document.getElementById(action).innerHTML ="Please type something";
            }
            
        }
        function create_new_prompts_inputs(loading_old_data,element){
                interation++;
                //Creating elemenst dinamically
                var newDiv = document.createElement("div");
                newDiv.setAttribute("id", "block"+number_new_block);

                var newTextarea = document.createElement("textarea");
                newTextarea.setAttribute("id", "prompt"+number_new_block);
                newTextarea.setAttribute("rows", "20");
                newTextarea.setAttribute("cols", "70");
                newTextarea.setAttribute("oninput",`inserting_into_db('${number_new_block}')`);
                if(loading_old_data){
                    newTextarea.innerHTML=element.prompt_content;
                }
                
                var newButton = document.createElement("button");
                newButton.innerHTML = "Send to AI";
                newButton.setAttribute("onclick",`send_ai(${number_new_block})`);
                newButton.setAttribute("type",`submit`);
                newButton.setAttribute("class",`btn btn-primary`);
                
                var newIterationButton = document.createElement("button");
                newIterationButton.innerHTML = "New iteration +";
                newIterationButton.setAttribute("onclick",`new_interaction('${number_new_block}')`);
                newIterationButton.setAttribute("type",`submit`);
                newIterationButton.setAttribute("class",`btn btn-primary`);

                var newP = document.createElement("p");
                newP.setAttribute("id", "action"+number_new_block);
                newP.appendChild(newTextarea);
                
                //Adding elemensts to the new div
                newDiv.appendChild(newTextarea);
                newDiv.appendChild(newButton);
                newDiv.appendChild(newIterationButton);
                newDiv.appendChild(newP);
                //Adding de new div to the body
                let container=document.getElementById("blocks_container");
                container.appendChild(newDiv);
                //Updating new values of block
                number_actual_block++;
                number_new_block++;
        }
        function new_interaction(){
            if(document.getElementById("prompt"+number_actual_block).value!=""){
                document.getElementById("action"+number_actual_block).innerHTML ="";     
                create_new_prompts_inputs(false);
                //Adding new element to the array which contorls the insertions or updates of the prompts
                control_insertion_prompts[number_actual_block]=true;
            }else{
                document.getElementById(name_last_action).innerHTML ="Please type something before adding other iteration";
            }
        }
        
        function inserting_into_db(number) {
            //ID of elements
            let prompt="prompt"+number;
            let action="action"+number;
            var input = document.getElementById(prompt).value;    
            if(control_insertion_prompts[number]){
                //New insertion of the data
                $.ajax({
                        type : "POST",  
                        url  : "back.php",  
                        data : { prompt : input ,attempt:interation,customer_id:1,iterations:number,id_prompt:ids_prompts[1]},
                        success: function(res){  
                            if(interation==0){
                                ids_prompts[number]=res;
                            }                      
                        }
                    });
                control_insertion_prompts[number]=false;
            }else{
                //Nupdte of the current prompt
                if(number==1){
                    ini=1;
                }else{
                    ini=0;
                }
                console.log(ids_prompts[1]);
                $.ajax({
                        type : "POST", 
                        url  : "back.php",  
                        data : { prompt : input,prompt_id:ids_prompts[1],customer_id:1,iterations:number,initial:ini},
                        success: function(res){ 
                        }
                    });
            }
        }
    </script>
</html>