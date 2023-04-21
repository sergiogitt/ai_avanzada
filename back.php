<?php
require "src/bd_config.php";
try {
    $conection = new PDO("mysql:host=".SERVIDOR_BD.";dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch(PDOException $e) {
    exit("Connection error: " . $e->getMessage());
}
if (isset($_POST['prompt'])) {
  
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
                $query="update tbl_initial_prompt set prompt=? where PROMPT_ID=?";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["prompt"];
                $data[]=$_POST["prompt_id"];
                $sentence->execute($data);
                echo "Tabla inicial";
            }else{ 
                $query="update tbl_prompt_iterations set prompt_content=? where prompt_iteration=? && PROMPT_ID=?";
                $sentence=$conection->prepare($query);
                $data[]=$_POST["prompt"];
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
if(isset($_POST['getFirstData'])){
    try
   {
     
       $query="select * from tbl_initial_prompt where customer_ID=?";
       $sentence=$conection->prepare($query);
       $data[]=$_POST["customer_id"];
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
if(isset($_POST['getSecondData'])){
    try
   {
     
       $query="select * from tbl_prompt_iterations where PROMPT_ID=?";
       $sentence=$conection->prepare($query);
       $data[]=$_POST["prompt_id"];
       $sentence->execute($data);
       echo  json_encode($sentence->fetchAll(PDO::FETCH_ASSOC));
       
   }
   catch(PDOException $e)
   {
       echo "Cant execute the query. Error:".$e->getMessage();
   }

}
?>

