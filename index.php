<?php
require "src/bd_config.php";


if (isset($_POST['prompt'])) {
    try {
        $conexion = new PDO("mysql:host=".SERVIDOR_BD.":3307;dbname=".NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    }
    catch(PDOException $e) {
        exit("Connection error: " . $e->getMessage());
    }
    
    if(isset($_POST['attempt'])){
        try
        {
            $consulta="insert into tbl_initial_prompt (customer_ID, iterations, prompt) values (?,?,?)";
            $sentencia=$conexion->prepare($consulta);
            $datos[]=$_POST["customer_id"];
            $datos[]=$_POST["iterations"];
            $datos[]=$_POST["prompt"];
            $sentencia->execute($datos);

            echo $conexion->lastInsertId();
        }
        catch(PDOException $e)
        {
        
            echo "Imposible realizar la consulta. Error:".$e->getMessage();
        }
    }else{
        try
        {
            $consulta="update tbl_initial_prompt set prompt=? where PROMPT_ID=?";
            $sentencia=$conexion->prepare($consulta);
            $datos[]=$_POST["prompt"];
            $datos[]=$_POST["prompt_id"];

            $sentencia->execute($datos);

            
        }
        catch(PDOException $e)
        {
        
            echo "Imposible realizar la consulta. Error:".$e->getMessage();
        }
    }
   
    
}
?>

