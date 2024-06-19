<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'db.php';

class Usuario
{
    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function creaUsuario($data)
    {
        try {

            if ($data['edit'] == 1) {
                $stmt = $this->_DB->SeguimientoPedidos()->prepare("UPDATE usuario SET Cedula = :cedula, 
                                                                                                    Nombre = :nombre, 
                                                                                                    Login = :login, 
                                                                                                    Pass = :password, 
                                                                                                    Perfil = :perfil, 
                                                                                                    canal = :canal, 
                                                                                                    ciudad = :ciudad WHERE Login = :login");
                $stmt->execute(array(
                    ':cedula' => $data['cedula'],
                    ':nombre' => $data['nombre'],
                    ':login' => $data['login'],
                    ':password' => $data['password'],
                    ':perfil' => $data['perfil'],
                    ':canal' => $data['canal'],
                    ':ciudad' => $data['ciudad'],
                    ':login' => $data['login'],

                ));
                if ($stmt->rowCount() == 1) {
                    $data = array('state' => true, 'msj' => 'Datos actualizados correctamente');
                } else {
                    $data = array('state' => false, 'msj' => 'No se encontraron datos');
                }
            } else {
                $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT * FROM usuario WHERE Login = :login");
                $stmt->execute(array(':login' => $data['login']));

                if ($stmt->rowCount()) {
                    return array('state' => false, 'msj' => 'Este login ya se encuentra registrado');
                }

                $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT * FROM usuario WHERE Cedula = :cedula");
                $stmt->execute(array(':cedula' => $data['cedula']));

                if ($stmt->rowCount()) {
                    return array('state' => false, 'msj' => 'La cédula ya se encuentra registrada');
                }

                $stmt = $this->_DB->SeguimientoPedidos()->prepare("INSERT INTO usuario (Cedula, Nombre, Login, Pass, Perfil, canal, ciudad, fecha_crea, usuario_crea)
                                                                                            VALUES (:cedula, :nombre, :login, md5(:pass), :perfil, :canal, :ciudad, :fecha_crea, :usuario_crea)");
                $stmt->execute(array(
                    ':cedula' => $data['cedula'],
                    ':nombre' => $data['nombre'],
                    ':login' => $data['login'],
                    ':pass' => $data['password'],
                    ':perfil' => $data['perfil'],
                    ':canal' => $data['canal'],
                    ':ciudad' => $data['ciudad'],
                    ':fecha_crea' => date('Y-m-d H:i:s'),
                    ':usuario_crea' => $data['usuario_crea']
                ));

                if ($stmt->rowCount() == 1) {
                    $data = array('state' => true, 'msj' => 'Usuario registrado exitosamente');
                } else {
                    $data = array('state' => false, 'msj' => 'Ha ocurrido un error interno, intentalo nuevamente en unos minutos');
                }
            }

            $this->_DB = '';
            return $data;

        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    public function deleteUsuario($data)
    {
        try {
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT estado from usuario WHERE Login = :login");
            $stmt->execute(array(':login' => $data['login']));
            if ($stmt->rowCount() == 1) {
                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($response[0]['estado'] == 'Activo') {
                    $estado = 'Inactivo';
                } else {
                    $estado = 'Activo';
                }
                $stmt = $this->_DB->SeguimientoPedidos()->prepare("UPDATE usuario SET estado = :estado WHERE Login = :login");
                $stmt->execute(array(':login' => $data['login'], ':estado' => $estado));
                if ($stmt->rowCount() == 1) {
                    $data = array('state' => true, 'msj' => 'El usuario con login ' . $data['login'] . ' Ahora esta ' . $estado);
                } else {
                    $data = array('state' => false, 'data' => 'Ha ocurrido un error interno intentalo nuevamente en unos minutos');
                }
            } else {
                $data = array('state' => false, 'data' => 'No se encontraron datos');
            }
            $this->_DB = '';
            return $data;
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    public function editaUsuario($data)
    {
        try {
            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT Cedula, Nombre, Login, Pass, Perfil, canal, ciudad from usuario WHERE Login = :login");
            $stmt->execute(array(':login' => $data['login']));
            if ($stmt->rowCount() == 1) {
                $data = array('state' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC));
            } else {
                $data = array('state' => false, 'data' => 'No se encontraron datos');
            }

            $this->_DB = '';
            return $data;
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    public function listUsuario($data)
    {
        try {
            $condicion = "";
            if ($data['Cedula'] != 0) {
                $pedido = $data['Cedula'];
                $condicion = " AND Cedula LIKE '%$pedido%' ";
            }

            $pagenum = $data['pageNumber'];
            $pagesize = $data['pageSize'];
            $offset = ($pagenum - 1) * $pagesize;

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT * from usuario ");
            $stmt->execute();
            $count = $stmt->rowCount();

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT
                                                                        a.Nombre,
                                                                        a.Cedula,
                                                                        a.Login,
                                                                        b.perfil,
                                                                        a.estado 
                                                                    FROM
                                                                        usuario a
                                                                        INNER JOIN perfil b ON a.perfil = b.id 
                                                                    WHERE
                                                                        1 = 1 $condicion
                                                                    LIMIT $offset, $pagesize");
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $data = array('state' => 1, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'count' => $count);
            } else {
                $data = array('state' => 0, 'msj' => 'No se encontraron registros');
            }

            $this->_DB = '';
            return $data;
        } catch (PDOException $e) {
            var_dump($e);
        }
    }

    public function guardaSolicitud($data)
    {
        try {

            //$pagina = 'Soporte comercial (GESCOM)';
            $pagina = 'soporte-comercial';
            $cc = trim($data['cc']);
            $email = trim($data['email']);
            $nombre = str_replace(' ', '-', $data['nombre']);;
            $observacion = str_replace(' ', '-', $data['observacion']);;
            $usuario = trim($data['usuario']);

            $datos = ['email' => $email, 'plataforma' => $pagina, 'cc' => $cc, 'nombre' => $nombre, 'usuario' => $usuario, 'observacion' => $observacion];

            $json_data = json_encode($datos);
            $url = "http://10.100.66.254/BB8/contingencias/Buscar/guardaSolicitud/$json_data";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, "$url");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            $data = curl_exec($ch);
            curl_close($ch);

            $dataclick = json_decode($data, true);

            $data = (object)$dataclick;

            if ($data->state) {
                $response = ['state' => true, 'msg' => $data->msg];
            } else {
                $response = ['state' => false, 'msg' => $data->msg];
            }

            return $response;

        } catch (PDOException $th) {
            var_dump($th->getMessage());
        }
    }
}