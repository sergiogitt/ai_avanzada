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
        let control_insertion_prompts=[];        
        control_insertion_prompts[1]=true;
        function test(){
            console.log("go")
        }
        function send_ai(number){
            //ID of elements
            let prompt="prompt"+number;
            let action="action"+number;
            //Control if there is something on the textarea
            if(document.getElementById(prompt).value!=""){
                const REACT_APP_OPENAI_API_KEY="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";
                let data = {
                prompt: document.getElementById(prompt).value,
                max_tokens: 5,
                temperature: 0.5
                };
                //Call to the Open ai To DO
                fetch('https://api.openai.com/v1/engines/davinci-codex/completions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${REACT_APP_OPENAI_API_KEY}`
                },
                body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(json => {
                    // Parse the JSON response and update the action element
                    document.getElementById(action).innerHTML = json.choices[0].text;
                })
                .catch(error => {
                    // Handle errors
                    console.error(error);
                    document.getElementById(action).innerHTML = "Error: " + error.message;
                });
                

            }else{
                document.getElementById(action).innerHTML ="Please type something";
            }
            
        }
        function new_interaction(){
            let name_prompt="prompt"+number_actual_block;
            let name_last_action="action"+number_actual_block;
            if(document.getElementById(name_prompt).value!=""){
                document.getElementById(name_last_action).innerHTML ="";     
                interation++;
                //Creating elemenst dinamically
                var newDiv = document.createElement("div");
                newDiv.setAttribute("id", "block"+number_new_block);

                var newTextarea = document.createElement("textarea");
                newTextarea.setAttribute("id", "prompt"+number_new_block);
                newTextarea.setAttribute("rows", "20");
                newTextarea.setAttribute("cols", "70");
                newTextarea.setAttribute("oninput",`inserting_into_db('${number_new_block}')`);
                
                var newButton = document.createElement("button");
                newButton.innerHTML = "Send to AI";
                newButton.setAttribute("onclick",`send_ai('${number_new_block}')`);
                
                var newIterationButton = document.createElement("button");
                newIterationButton.innerHTML = "New iteration +";
                newIterationButton.setAttribute("onclick",`new_interaction('${number_new_block}')`);

                var newP = document.createElement("p");
                newP.setAttribute("id", "action"+number_new_block);
                newP.appendChild(newTextarea);
                
                //Adding elemensts to the new div
                newDiv.appendChild(newTextarea);
                newDiv.appendChild(newButton);
                newDiv.appendChild(newIterationButton);
                newDiv.appendChild(newP);
                //Adding de new div to the body
                document.body.appendChild(newDiv);
                //Updating new values of block
                number_actual_block++;
                number_new_block++;
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
                let ini=0;
                if(number==1){
                    ini=1;
                }
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