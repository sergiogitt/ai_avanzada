
     const CUSTOMER_ID=2;
        let number_actual_block=1;
        let number_new_block=number_actual_block+1;
        let ids_prompts=[];
        let interation=0;
        let control_insertion_prompts=[];        
        control_insertion_prompts[1]=true;
        let getting_info=false;
        let companies_found=[];
        let buttons_to_disable=document.getElementsByTagName('button');
        function get_actual_data_user(user_id){
            api_call("back.php",JSON.stringify({functionName: 'getFirstData', args: [CUSTOMER_ID]}),{},fill_first_field_with_data,null);
        }
        function fill_first_field_with_data(answer){
            if(answer.not_found!="Not found that client"){
                control_insertion_prompts[1]=false;
                getting_info=true;
                ids_prompts[1]=answer.PROMPT_ID;
                console.log(ids_prompts)
                document.getElementById("prompt1").value=answer.prompt;
                api_call("back.php",JSON.stringify({functionName: 'getSecondData', args: [ids_prompts[1]]}),{},fill_all_info);
               
            }
        }
        function fill_all_info(data){
            data.forEach(element => {
                    create_new_prompts_inputs(true,element);
                    //Adding new element to the array which contorls the insertions or updates of the prompts
                    control_insertion_prompts[number_actual_block]=false;
                });
        }
        function containsURL(str) {
            const regex = /(((https?:\/\/)|(www\.))[^\s]+)/g;
            return str.match(regex);
        }
        function name_company(response){
            let i=0;
                           
            urls_found.forEach(element => {
                companies_found[i]=response[i];
                i++;
            });
        }
        function send_ai(number){
            //ID of elements
            let prompt_id="prompt"+number;
            let action="action"+number;
            
            let prompt_content=document.getElementById(prompt_id).value;
            if(prompt_content!=""){
                document.getElementById("loading").setAttribute('class',"spinner-border");
                //Control if there is something on the textarea
                document.getElementById(prompt_id).setAttribute('disabled', true);
                for (let i = 0; i < buttons_to_disable.length; i++) {
                    buttons_to_disable[i].setAttribute('disabled', true);
                }
                const REACT_APP_OPENAI_API_KEY="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";
               // Define the data variable with prompt and API parameters
               const API_ENDPOINT = "https://api.openai.com/v1/chat/completions";

                let urls_found=containsURL(prompt_content);
                let header={ "Content-Type": "application/json","Authorization": "Bearer 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"};
                if(urls_found!=null){
                    
                    fetch('apis.php', {
                        method: 'POST',
                        body: JSON.stringify({ functionName: 'getCompany', args: [urls_found] }),
                        })
                        .then(response => response.text())
                        .then(data => {
                            let response=JSON.parse(data) 
                            let i=0;
                           
                            urls_found.forEach(element => {
                                companies_found[i]=response[i];
                                i++;
                            });
                        }
                        ).then( data=>{
                            
                            for(let i=0;i<urls_found.length;i++){
                                api_call("back.php", JSON.stringify({functionName: 'get_company', args: [urls_found[i] ,companies_found[i][0],companies_found[i][1],companies_found[i][3],companies_found[i][2]]}),{})
                            }
                           
                        })
                        .catch(error => console.error(error));
                        
                            
                        fetch('apis.php', {
                        method: 'POST',
                        body: JSON.stringify({ functionName: 'getScrape', args: [urls_found] }),
                        })
                        .then(response => response.text())
                        .then(data => {
                            let response=JSON.parse(data) 
                            let i=0;
                            urls_found.forEach(element => {
                                prompt_content=prompt_content.replace(element,response[i]);
                                i++
                            });                            
                        }
                        ).then( response=>{
                            api_call(API_ENDPOINT, JSON.stringify({
                                "model": "gpt-3.5-turbo",
                                "messages": [{"role": "user", "content": prompt_content}],
                                "temperature": 0.7
                            }), header, disable_actions_on_call,number);
                    })
                        .catch(error => console.error(error)
                    );          
                }else{
                    
                    api_call(API_ENDPOINT, JSON.stringify({
                        "model": "gpt-3.5-turbo",
                        "messages": [{"role": "user", "content": prompt_content}],
                        "temperature": 0.7
                    }), header, disable_actions_on_call,number);
                    
                }
            }else{
                document.getElementById(action).innerHTML ="Please type something";
            }
            
        }
        function load_process(){
            let prompt_content=data.choices[0].message.content;
            var newP = document.getElementById("response_ai");
            newP.innerHTML=prompt_content;
            document.getElementById("loading").removeAttribute('class');
            for (let i = 0; i < buttons_to_disable.length; i++) {
                buttons_to_disable[i].removeAttribute('disabled');
            }
            document.getElementById(prompt_id).removeAttribute('disabled');
            document.getElementById("response").appendChild(newP);
            let num=number;
            num++;
            let next_prompt="prompt"+(num);
            if(document.getElementById(next_prompt)==null){
                create_new_prompts_inputs(false);
                api_call("back.php", JSON.stringify({functionName: 'insertNewInitialPrompt', args: [input ,interation,1]}),null,set_id_prompt,number)
                control_insertion_prompts[number]=false;
            }
            document.getElementById(next_prompt).value=document.getElementById(next_prompt).value+"\nAPI RESPONE: "+prompt_content;        
        }
        function api_call(url,body_content,header_content,todo=null,args){
            fetch(url, {
            headers:header_content,
            method: 'POST',
            body:body_content,
            })
            .then(response => response.json())
            .then(data => {
                if(todo){
                    todo(data,args);
                }
               
            }
            )
            .catch(error => console.error(error));
        }
        
        function disable_actions_on_call(data,number){
            let prompt_id="prompt"+number;
            let content=data.choices[0].message.content;
            var newP = document.getElementById("response_ai");
            newP.innerHTML=content;
            document.getElementById("loading").removeAttribute('class');
            for (let i = 0; i < buttons_to_disable.length; i++) {
                buttons_to_disable[i].removeAttribute('disabled');
            }
            document.getElementById(prompt_id).removeAttribute('disabled');
            document.getElementById("response").appendChild(newP);
            let num=number;
            num++;
            let next_prompt="prompt"+(num);
            if(document.getElementById(next_prompt)==null){
                create_new_prompts_inputs(false);

                api_call("back.php", JSON.stringify({functionName: 'insertNewIterationPrompt', args: [ ids_prompts[1] ,num,content]}),{})
                control_insertion_prompts[number]=false;
            }
            document.getElementById(next_prompt).value=document.getElementById(next_prompt).value+"\nAPI RESPONE: "+content;
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
                    var remove_iteration_id=element.prompt_iteration;
                    var remove_prompt_id=element.PROMPT_ID;
                    var newRemoveIterationButton = document.createElement("button");
                    newRemoveIterationButton.innerHTML = "Remove iteration -";
                    newRemoveIterationButton.setAttribute("onclick","remove_interaction('"+remove_iteration_id+"','"+remove_prompt_id+"')");
                    newRemoveIterationButton.setAttribute("class",`btn btn-primary`);
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
                var div_buttons=document.createElement("div");
                div_buttons.setAttribute("class","buttons");
                div_buttons.appendChild(newButton);
                div_buttons.appendChild(newIterationButton);
                if(loading_old_data){
                    div_buttons.appendChild(newRemoveIterationButton);
                }
                var newP = document.createElement("p");
                newP.setAttribute("id", "action"+number_new_block);
                newP.appendChild(newTextarea);
                
                //Adding elemensts to the new div
                newDiv.appendChild(newTextarea);
                newDiv.appendChild(div_buttons);
                newDiv.appendChild(newP);
                //Adding de new div to the body
                let container=document.getElementById("blocks_container");
                container.appendChild(newDiv);
                //Updating new values of block
                number_actual_block++;
                number_new_block++;
        }
        function remove_interaction(iteration,prompt_id){
            $.ajax({
                type : "POST",  
                url  : "back.php",  
                data : { remove_interaction : iteration, prompt_id:prompt_id},
                success: function(res){  
                                   
                }
            });
            location.reload();
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
        function set_id_prompt(res,number){
            if(interation==0){
                ids_prompts[number]=res;
            }
        }
        
        
        function inserting_into_db(number) {
            //ID of elements
            let prompt="prompt"+number;
            let action="action"+number;
            var input = document.getElementById(prompt).value;    
            if(control_insertion_prompts[number]){
                //New insertion of the data url,body_content,todo,args
                if(interation==0){
                    api_call("back.php", JSON.stringify({functionName: 'insertNewInitialPrompt', args: [input ,interation,CUSTOMER_ID]}),{},set_id_prompt,number)
                    console.log(ids_prompts[number])
                }else{
                    console.log(ids_prompts[number])
                    api_call("back.php", JSON.stringify({functionName: 'insertNewIterationPrompt', args: [ ids_prompts[1],number,input]}),{})
                }
                
                
                control_insertion_prompts[number]=false;
            }else{
                //Update of the current prompt
                if(number==1){
                    api_call("back.php", JSON.stringify({functionName: 'updateInitialPrompt', args: [input ,ids_prompts[1]]}),{})
                }else{
                    //Update of the nexts prompts
                    api_call("back.php", JSON.stringify({functionName: 'updateIterationPrompt', args: [input ,number,ids_prompts[1]]}),{})
                }
            }
        }
