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
            $query="insert into tbl_initial_prompt (customer_ID, iterations, prompt) values (?,?,?)";
            $sentence=$conection->prepare($query);
            $data[]=$_POST["customer_id"];
            $data[]=$_POST["iterations"];
            $data[]=$_POST["prompt"];
            $sentence->execute($data);
            echo $conection->lastInsertId();
        }
        catch(PDOException $e)
        {     
            echo "Cant execute the query. Error:".$e->getMessage();
        }
    }else{
        try
        {
            $query="update tbl_initial_prompt set prompt=? where PROMPT_ID=?";
            $sentence=$conection->prepare($query);
            $data[]=$_POST["prompt"];
            $data[]=$_POST["prompt_id"];
            $sentence->execute($data);
        }
        catch(PDOException $e)
        {
            echo "Cant execute the query. Error:".$e->getMessage();
        }
    }
}
?>

