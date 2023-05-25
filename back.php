<?php
require "src/bd_config.php";
ini_set('display_errors', 1);



$data = json_decode(file_get_contents('php://input'), true);
if(isset($data['functionName'])){
    $functionName = $data['functionName'];
    $args = $data['args'];
    if (function_exists($functionName)) {
        $result = call_user_func_array($functionName, $args);
        
    } 
}
function saveFile($content,$name,$id){
    
    try
    {
        $folder_user="./uploaded_files/".$id."-files";
        $name=explode(".",str_replace(' ', '-', $name))[0];
        $new_file_name = $id."_".$name."_".time();
        if(!is_dir($folder_user)){
            mkdir($folder_user);
        }
        $file = $folder_user."/".$new_file_name;  // Specify the file path and name

        // Write the content to the file
        $result = file_put_contents($file, $content);

        // Check if the operation was successful
        if ($result !== false) {
            echo json_encode(["uploaded"=> 'File created and content saved successfully.']);
        } else {
            echo json_encode(["error"=>  'Failed to create the file or save the content.']);
        }
       
    }
    catch(PDOException $e)
    {     
        echo "Can't execute the queries. Error: ".$e->getMessage();
    }
}
function mergeFiles($id){
    
    try
    {
        $folder_user="./uploaded_files/".$id."-files";
        $files = scandir($folder_user);
        if(!$files){
            mkdir($folder_user);
        }

        $mergedContent = ''; // Variable to store the merged content

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                // Read the content of each file
                $content = file_get_contents($folder_user ."/". $file);
                
                // Append the content to the merged content variable
                $mergedContent .= $content." ";
                unlink($folder_user ."/". $file);
            }
        }
        $mergedFilePath =$folder_user."/".$id."_merged_".time();

        file_put_contents($mergedFilePath, $mergedContent);
        echo json_encode(["correct"=> 'Merged']);

    }
    catch(PDOException $e)
    {     
        echo "Can't execute the queries. Error: ".$e->getMessage();
    }
}
function mergedFiles($id){
    
    try
    {
        $folder_user="./uploaded_files/".$id."-files";
        if(!is_dir($folder_user)){
            mkdir($folder_user);
        }
        $files = scandir($folder_user);
       
        $mergedContent = ''; // Variable to store the merged content

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                // Read the content of each file
                $content = file_get_contents($folder_user ."/". $file);
                
                // Append the content to the merged content variable
                $mergedContent .= $content." ";
                unlink($folder_user ."/". $file);
            }
        }
        $mergedFilePath =$folder_user."/".$id."_merged_".time();

        file_put_contents($mergedFilePath, $mergedContent);
        //echo json_encode(["correct"=> 'Merged']);

    }
    catch(PDOException $e)
    {     
        echo "Can't execute the queries. Error: ".$e->getMessage();
    }
}
function getMemory($id,$prompt){
    
    try
    {
        $folder_user="./uploaded_files/".$id."-files";
        if(!is_dir($folder_user)){
            mkdir($folder_user);
        }
        $files = scandir($folder_user);
        if(count($files)>1){
            mergedFiles($id);
        }
        $file = scandir($folder_user)[2];
        $content = file_get_contents($folder_user ."/". $file);
        $content=summariseContent($folder_user."/".$file,"Summarise and leave all technichal and important information in this text: ".$content);
        echo json_encode(["memory"=> $content]);

    }
    catch(PDOException $e)
    {     
        echo "Can't execute the queries. Error: ".$e->getMessage();
    }
}
function summariseContent($folder_user,$prompt){
    $endpoint = "https://api.openai.com/v1/chat/completions";
    $apiKey = "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";

    $headers = array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    );

    $data = array(
        "model" => "gpt-3.5-turbo",
        "messages" => array(
            array(
                "role" => "user",
                "content" => $prompt
            )
        ),
        "temperature" => 0.7
    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Handle the response
    if ($response === false) {
        // Request failed
        echo "Error: " . curl_error($ch);
    } else {
        // Request succeeded
        $responseData = json_decode($response, true);
        file_put_contents($folder_user, $responseData["choices"][0]["message"]["content"]);
        return $responseData["choices"][0]["message"]["content"];

        // Process the response data as needed
       
}
}
function insert_file_on_db($location,$id){
    try
    {
        $conection=start_conection();
        $query="insert into tbl_file (file_location, file_owner_id) values (?,?)";
        $sentence=$conection->prepare($query);
 
        $data[]=$location;
        $data[]=$id;
       
        $sentence->execute($data);

       
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the query. Error:".$e->getMessage();
    }
}
function create_file_table(){
    try
    {
        $conection=start_conection();
        $query='CREATE TABLE if not exists tbl_file  (
            file_id INT AUTO_INCREMENT PRIMARY KEY,
            file_location VARCHAR(255),
            file_owner_id INT,
            FOREIGN KEY (file_owner_id) REFERENCES tbl_users(ID)
          );';
        $sentence=$conection->prepare($query);
 
       
        $sentence->execute([]);

       
    }
    catch(PDOException $e)
    {     
        echo json_encode(["error"=>"Cant execute the query. Error:".$e->getMessage()]);
    }
}



function start_conection(){
    try {
        $conection = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    }
    catch(PDOException $e) {
        exit("Connection error: " . $e->getMessage());
    }
    return $conection;
}
function deleteFile($file,$id){
    $filePath="./uploaded_files/".$id."-files/";
    if (file_exists($filePath.$file[0])) {
        if (unlink($filePath.$file[0])) {
            echo  json_encode(["file_deleted"=>"File deleted successfully."]);
        } else {
            echo  json_encode(["error"=>"Unable to delete the file."]);
        }
    } else {
        echo  json_encode(["not_found"=>$filePath.$file[0]]);
    }

    
}
function getFIles($id){
    create_file_table();
    $folder_user="./uploaded_files/".$id."-files";
    // Obtener el listado de archivos y directorios en el directorio especificado
    $files=[];
    if(is_dir($folder_user)){
        $archivos = scandir($folder_user);
        
        // Recorrer el listado de archivos y directorios
        foreach ($archivos as $archivo) {
            // Excluir los directorios "." y ".."
            if ($archivo !== '.' && $archivo !== '..') {
                // Realizar alguna acción con el archivo
            $files[]=$archivo;
            }
        }
    }
    
    echo json_encode($files);
}
function insertNewInitialPrompt($prompt,$iteration,$user_id){
    try
    {
        $conection=start_conection();
        $query="insert into tbl_initial_prompt (customer_ID, iterations, prompt) values (?,?,?)";
        $sentence=$conection->prepare($query);
 
        $data[]=$user_id;
        $data[]=$iteration;
        $data[]=$prompt;
        $sentence->execute($data);

        echo $conection->lastInsertId();
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the query. Error:".$e->getMessage();
    }
}

function insertNewIterationPrompt($id_prompt,$iteration,$prompt){
    try
    {
        $conection=start_conection();
        $query="insert into tbl_prompt_iterations (PROMPT_ID, prompt_iteration, prompt_content) values (?,?,?)";
        $sentence=$conection->prepare($query);
        $data[]=$id_prompt;
        $data[]=$iteration;
        $data[]=$prompt;
       
        $sentence->execute($data);

        echo $conection->lastInsertId();
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the querys. Error:".$e->getMessage();
    }
}

function updateInitialPrompt($prompt,$prompt_id){
    try
    {
        $conection=start_conection();
        $query="update tbl_initial_prompt set prompt=?,date_last_change=? where PROMPT_ID=?";
        $sentence=$conection->prepare($query);
        $data[]=$prompt;
        $data[]=date("Y-m-d H:i:s");
        $data[]=$prompt_id;
        $sentence->execute($data);
        echo json_encode(["updated"=>"Prompt updated correctly"]);
       
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the query. Error:".$e->getMessage();
    }
}
function updateIterationPrompt($prompt,$iteration,$prompt_id){
    try
    {
        $conection=start_conection();
        $query="update tbl_prompt_iterations set prompt_content=?,date_last_change=? where prompt_iteration=? && PROMPT_ID=?";
        $sentence=$conection->prepare($query);
        $data[]=$prompt;
        $data[]=date("Y-m-d H:i:s");
        $data[]=$iteration[0];
        $data[]=$prompt_id;
        $sentence->execute($data);
        echo json_encode(["updated"=>"Prompt updated correctly"]);
       
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the query. Error:".$e->getMessage();
    }
}
function getLastId($prompt_id){
    try
   {
    $conection=start_conection();
    $query="select * from tbl_prompt_iterations where PROMPT_ID=? order by PROMPT_ID desc limit 1";
    $sentence=$conection->prepare($query);
    $data[]=$prompt_id;
    $sentence->execute($data);
    if($sentence->rowCount()==1){
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC)["prompt_iteration"]);
    }else{
        echo  json_encode(["not_found"=>"Not found that client"]);
    }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
function getFirstData($customer_id){
    try
   {
    $conection=start_conection();
    $query="select * from tbl_initial_prompt where customer_ID=?";
    $sentence=$conection->prepare($query);
    $data[]=$customer_id;
    $sentence->execute($data);
    if($sentence->rowCount()>0){
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC));
    }else{
        echo  json_encode(["not_found"=>"Not found that client".$customer_id]);
    }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
if (isset($_POST["logUser"])){

    try
   {
    $conection=start_conection();
    $query="select * from tbl_users where display_name=? and password=?";
    $sentence=$conection->prepare($query);
    $data[]=$_POST["user"];
    $data[]=md5($_POST["password"]);
    $sentence->execute($data);
    if($sentence->rowCount()>0){
        session_start();
        $_SESSION["user"]=$_POST["user"];
        $_SESSION["password"]=md5($_POST["password"]);
        $result=$sentence->fetch(PDO::FETCH_ASSOC);
        $_SESSION["ID"]=$result["ID"];
        $_SESSION["rol"]=$result["rol"];
       
       header("Location:autogpt.php");
       exit;
        
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC));
    }else{
        echo  json_encode(["not_found"=>"Not found that client".$_POST["user"]." ".$_POST["password"]]);
    }
       
       
   }
   catch(PDOException $e)
   {
       echo  json_encode(["internal_error"=>"Cant execute the query. Error:".$e->getMessage()]);
   }

}
function logUser($user,$password){
    try
   {
    $conection=start_conection();
    $query="select * from tbl_users where display_name=? and password=?";
    $sentence=$conection->prepare($query);
    $data[]=$user;
    $data[]=$password;
    $sentence->execute($data);
    if($sentence->rowCount()>0){
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC));
    }else{
        echo  json_encode(["not_found"=>"Not found that client"]);
    }
       
       
   }
   catch(PDOException $e)
   {
       echo  json_encode(["internal_error"=>"Cant execute the query. Error:".$e->getMessage()]);
   }

}



function getSecondData($prompt_id){
    try
   {
    $conection=start_conection();
    $query="select * from tbl_prompt_iterations where PROMPT_ID=?";
    $sentence=$conection->prepare($query);
    $data[]=$prompt_id;
    $sentence->execute($data);
    echo  json_encode($sentence->fetchAll(PDO::FETCH_ASSOC));
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}

if (isset($_POST['prompt'])) {
  echo "entre";
    if(isset($_POST['attempt'])){
        try
        {
            if($_POST['attempt']==0){
                $query="insert into tbl_initial_prompt (customer_ID, iterations, prompt) values (?,?,?)";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["customer_id"];
                $data[]=$_POST["iterations"];
                $data[]=$_POST["prompt"];
                $sentence->execute($data);
            }else{
                $query="insert into tbl_prompt_iterations (PROMPT_ID, prompt_iteration, prompt_content) values (?,?,?)";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["id_prompt"];
                $data[]=$_POST["iterations"];
                $data[]=$_POST["prompt"];
                $sentence->execute($data);
            }

            echo $conection->lastInsertId();
        }
        catch(PDOException $e)
        {     
            echo "Cant execute the query. Error:".$e->getMessage();
        }
    }else{
        echo "inside";
        try
        {
            if($_POST['initial']==1){
                $query="update tbl_initial_prompt set prompt=?,date_last_change=? where PROMPT_ID=?";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["prompt"];
                $data[]=date("Y-m-d H:i:s");
                $data[]=$_POST["prompt_id"];
                $sentence->execute($data);
                echo "Tabla inicial";
            }else{ 
                $query="update tbl_prompt_iterations set prompt_content=?,date_last_change=? where prompt_iteration=? && PROMPT_ID=?";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["prompt"];
                $data[]=date("Y-m-d H:i:s");
                $data[]=$_POST["iterations"];
                $data[]=$_POST["prompt_id"];

                $sentence->execute($data);
                echo "Tabla siguiente";
            }
           
            
        }
        catch(PDOException $e)
        {
            echo "Cant execute the query. Error:".$e->getMessage();
        }
    }
   
}
function get_company($new_companie ,$description1,$description2,$name,$tags){
    try
   {
    $conection=start_conection();
    $query="select * from tbl_url where id_url=?";
    $sentence=$conection->prepare($query);
    $data[]=$new_companie;
    $sentence->execute($data);
    if($sentence->rowCount()==0){
        $description=$description1;
        if($description!=$description2){
            $description.=$description2;
        }
        $query="insert into tbl_url (id_url,description,tags,name) values (?,?,?,?)";
        $sentence=$conection->prepare($query);
        $data[]=$description;
        $data[]=$tags;
        $data[]=$name;
        $sentence->execute($data);
    }else{
        echo json_encode(["already_registeres"=>"Url registeres on DB"]);
    }
      
      
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}

if(isset($_POST['delete_user'])){
    try
   {
    $conection=start_conection();
       $query="delete  from tbl_users where ID=?";
       $sentence=$conection->prepare($query);
       $data[]=$_POST["delete_user"];

       $sentence->execute($data);
       if($sentence->rowCount()>0){
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC));
       }else{
        echo  json_encode(["not_found"=>"Not found that client"]);
       }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
if(isset($_POST['remove_interaction'])){
    try
   {
    $conection=start_conection();
       $query="delete  from tbl_prompt_iterations where PROMPT_ID=? and prompt_iteration=?";
       $sentence=$conection->prepare($query);
       $data[]=$_POST["prompt_id"];
       $data[]=$_POST["remove_interaction"];

       $sentence->execute($data);
       if($sentence->rowCount()>0){
        echo  json_encode($sentence->fetch(PDO::FETCH_ASSOC));
       }else{
        echo  json_encode(["delete"=>"Iteration".$_POST["remove_interaction"]." removed succesfully"]);
       }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
if(isset($_POST['getUsers'])){
    try
   {
    $conection=start_conection();
       $query="select * from tbl_users";
       $sentence=$conection->prepare($query);
       
       $sentence->execute([]);
       if($sentence->rowCount()>0){
        echo  json_encode($sentence->fetchAll(PDO::FETCH_ASSOC));
       }else{
        echo  json_encode(["not_users"=>"Users are empty"]);
       }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
if(isset($_POST['new_user'])){
    try
   {
    $conection=start_conection();
       $query="insert into tbl_users (display_name) values (?)";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["new_user"];
                
                $sentence->execute($data);
       }
       
       
   
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
if(isset($_POST['new_name'])){
    try
   {
     
    $query="update tbl_users set display_name=? where ID=?";
    $sentence=$conection->prepare($query);
    $data[]=$_POST["new_name"];
    $data[]=$_POST["edit_user"];
    $sentence->execute($data);   
    }
       
       
   
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}


?>

