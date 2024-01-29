<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'db.php';

class Contador
{
    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function Contador($data)
    {
        try {
            $fecha = date('Y-m-d');
            $stmt = $this->_DB->seguimiento()->prepare("SELECT * FROM Contador WHERE Fecha =:Fecha AND Modulo =:Modulo");
            $stmt->execute(array(':Fecha' => $fecha, ':Modulo' => $data));

            if ($stmt->rowCount() > 0) {
                $stmt = $this->_DB->seguimiento()->prepare("UPDATE Contador SET Contador = Contador+1 WHERE Fecha =:Fecha AND Modulo =:Modulo");
                $stmt->execute(array(':Fecha' => $fecha, ':Modulo' => $data));
            } else {
                $stmt = $this->_DB->seguimiento()->prepare("INSERT INTO Contador (Modulo,Fecha,Contador) VALUES (:Modulo,:Fecha,1)");
                $stmt->execute(array(':Fecha' => $fecha, ':Modulo' => $data));
            }

            $this->_BD = null;

        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }

    }
}