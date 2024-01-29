<?php

    class DB {
        function ConexionBD(){

            $host='NETV-PSQL09-05';
            $dbname='Service Optimization';
            $username='BI_Clicksoftware';
            $pasword ='6n`Vue8yYK7Os4D-y';
            $puerto=1433;
    
    
            try{
                $conn = new PDO ("sqlsrv:Server=$host,$puerto;Database=$dbname",$username,$pasword);
                // echo "Se conectó correctamen a la base de datos";
            }
            catch(PDOException $exp){
                echo ("No se logró conectar correctamente con la base de datos: $dbname, error: $exp");
            }
    
            return $conn;
        }
    
        function ConexionBDGestionOperativa(){

            $host='AMEV-PSQL02-09,1439';
            $dbname='Gestion_Operativa';
            $username='usrenvanxper';
            $pasword ='qLUDVhtRT9fM8VinnJeP';
            $puerto=1439;
    
    
            try{
                $conn = new PDO ("sqlsrv:Server=$host,$puerto;Database=$dbname",$username,$pasword);
                //echo "Se conectó correctamen a la base de datos Gestion Operativa";
            }
            catch(PDOException $exp){
                echo ("No se logró conectar correctamente con la base de datos: $dbname, error: $exp");
            }
    
            return $conn;
        }

        function SeguimientoPedidos(){
            
             $tipo_de_base = 'mysql';
             $host = 'localhost';
             //$nombre_de_base = 'seguimientopedidos';
             $nombre_de_base = 'soporte_comercial';
             /* $usuario = 'root';
             $contrasena = '7iCMKyRgksM39f3ofbehgk'; */
             $usuario = 'seguimientocrud';
	         $contrasena = '3Po1Ep56L7WGa$mY';
             $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

             try {
                $conn = new PDO("$tipo_de_base:host=$host;dbname=$nombre_de_base",$usuario,$contrasena,$opciones);
            } catch (PDOException $e) {
                echo ("No se logró conectar correctamente con la base de datos: $nombre_de_base, error: $e");
            }

            return $conn;
        }

        function seguimiento(){

            $tipo_de_base = 'mysql';
            $host = 'localhost';
            $nombre_de_base = 'seguimientopedidos';
            /* $usuario = 'root';
            $contrasena = '7iCMKyRgksM39f3ofbehgk'; */
            $usuario = 'seguimientocrud';
            $contrasena = '3Po1Ep56L7WGa$mY';
            $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            try {
                $conn = new PDO("$tipo_de_base:host=$host;dbname=$nombre_de_base",$usuario,$contrasena,$opciones);
            } catch (PDOException $e) {
                echo ("No se logró conectar correctamente con la base de datos: $nombre_de_base, error: $e");
            }

            return $conn;
        }
    }