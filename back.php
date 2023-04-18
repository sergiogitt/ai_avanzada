<?php
require "src/bd_config.php";
if (isset($_POST['prompt'])) {
    try {
        $conection = new PDO("mysql:host=".SERVIDOR_BD.":3307;dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    }
    catch(PDOException $e) {
        exit("Connection error: " . $e->getMessage());
    }
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
?>

