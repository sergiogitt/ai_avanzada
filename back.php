<?php
require "src/bd_config.php";
if(isset($_FILES["file"])){
    try
    {
        var_dump($_FILES);
        $nombreArchivo = $_FILES["file"]['name'];
        $rutaArchivoTemp = $_FILES["file"]['tmp_name'];
        $rutaArchivoDestino = "./uploaded_files" . '/' . $nombreArchivo;
        
        if (move_uploaded_file($rutaArchivoTemp, $rutaArchivoDestino)) {
            header('Location: index.php');
            exit;
        } else {
            return json_encode(["error"=>"Something went wrong on the uploady"]);;
        }
        echo $conection->lastInsertId();
    }
    catch(PDOException $e)
    {     
        echo "Cant execute the querys. Error:".$e->getMessage();
    }
}

$data = json_decode(file_get_contents('php://input'), true);

$functionName = $data['functionName'];
$args = $data['args'];

if (function_exists($functionName)) {
  $result = call_user_func_array($functionName, $args);
  
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
        $data[]=$iteration;
        $data[]=$prompt_id;

        $sentence->execute($data);
        echo json_encode(["updated"=>"Prompt updated correctly"]);
       
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
        echo  json_encode(["not_found"=>"Not found that client"]);
    }
       
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
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

if(isset($_POST['remove_interaction'])){
    try
   {
     
       $query="delete  from tbl_prompt_iterations where PROMPT_ID=? and prompt_iteration=?";
       $sentence=$conection->prepare($query);
       $data[]=$_POST["prompt_id"];
       $data[]=$_POST["remove_interaction"];

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
if(isset($_POST['getUsers'])){
    try
   {
     
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

