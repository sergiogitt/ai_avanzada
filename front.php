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
    
        <textarea id="prompt" oninput="test()"></textarea>
        <button onclick="new_interaction()">Submit</button>
       
    <script>
        let first_input=true;
        let id_prompt=null;
        let interation=0;
        function new_interaction(){
            interation++;
            document.getElementById("prompt").value=""; 
            first_input=true;
        }
        function test(content) {
            var input = document.getElementById("prompt").value;          
            if(first_input){
                console.log("Inserting "+document.getElementById('prompt').value);
                $.ajax({
                        type : "POST",  
                        url  : "index.php",  
                        data : { prompt : input ,attempt:true,customer_id:1,iterations:interation},
                        success: function(res){  
                            console.log("bien"+ res)
                            id_prompt=res;
                        }
                    });
                first_input=false;
            }else{
                console.log(id_prompt)
                $.ajax({
                        type : "POST", 
                        url  : "index.php",  
                        data : { prompt : input,prompt_id:id_prompt,customer_id:1,iterations:interation},
                        success: function(res){  
                            console.log("updating "+ res)
                        }
                    });
            }
        
        }
       
        
        
    </script>
</body>
</html>