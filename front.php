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
        <button>Submit</button>
       
    <script>
        let first_input=true;
        let id_prompt=null;
        function test(content) {
            var input = document.getElementById("prompt").value;
           
            if(first_input){
                console.log("Inserting "+document.getElementById('prompt').value);
                $.ajax({
                        type : "POST",  //type of method
                        url  : "index.php",  //your page
                        data : { prompt : input ,attempt:true,customer_id:1,iterations:1},// passing the values
                        success: function(res){  
                            console.log("bien"+ res)
                            id_prompt=res;
                        }
                    });
                first_input=false;

            }else{
                //console.log("Updating "+document.getElementById('prompt').value);
                console.log(id_prompt)
                $.ajax({
                        type : "POST",  //type of method
                        url  : "index.php",  //your page
                        data : { prompt : input,prompt_id:id_prompt,customer_id:1,iterations:1},// passing the values
                        success: function(res){  
                            console.log("updating "+ res)
                        }
                    });
            }
        
        }
       
        
        
    </script>
</body>
</html>