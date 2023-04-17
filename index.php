<?php
require "src/bd_config.php";
 try
    {
        $conexion=new PDO("mysql:hosst=".SERVIDOR_BD.":3307;dbname=".NOMBRE_BD,USUARIO_BD,CLAVE_BD,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        echo "Conection succed";
    }
    catch(PDOException $e)
    {
        exit("Conection error".$e->getMessage());
    }

?>