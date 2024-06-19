<?php

require_once '../Models/AuthModel.php';
require_once 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Auth
{
    private $model;
    private $_DB;

    public function __construct()
    {
        $this->model = new modelAuth();
        $this->_DB = new DB;
    }

    public function Login($data)
    {
        try {
            $Login = $data['username'];
            $pass = $data['password'];

            $ldapAuthenticator = new LdapAuthenticator();
            $result = $ldapAuthenticator->authenticate($Login, $pass);
            if ($result != "Login exitoso.") {
                echo json_encode(['state' => 2, 'msj' => 'Usuario y/o contraseña incorrecta']);
                die();
            }

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT Login, Perfil from usuario WHERE Login = :login");
            $stmt->execute(array(':login' => $Login));
            if (!($stmt->rowCount())) {
                echo json_encode(['state' => 3, 'msj' => 'Usuario no se encuentra registrado']);
                die();
            }

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT Login, Perfil FROM usuario WHERE Login = :login and estado = 'Activo'");
            $stmt->execute(array(':login' => $Login));
            $stmt->execute();
            if ($stmt->rowCount()) {
                $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT
                                                                            m.menu AS menu_principal,
                                                                            m.id AS id_principal,
                                                                            m.route AS route_principal,
                                                                            s.menu AS submenu,
                                                                            s.id AS id_submenu,
                                                                            s.route AS route_submenu
                                                                        FROM
                                                                            menu_perfil mp
                                                                            LEFT JOIN menu m ON mp.menu_id = m.id
                                                                            LEFT JOIN sub_menu s ON m.id = s.padre_id
                                                                        WHERE
                                                                            mp.perfil_id = :perfil
                                                                            AND mp.estado = 'Activo'
                                                                        ORDER BY
                                                                            m.menu, s.menu;");
                $stmt->execute(array(':perfil' => $user[0]['Perfil']));

                if ($stmt->rowCount()) {
                    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $data = array();
                    $data['Perfil'] = $user[0]['Perfil'];
                    $data['Login'] = $user[0]['Login'];
                    $data['menu'] = $menu;

                    $response = array('state' => 1, 'data' => $data);
                }

            } else {
                $response = array('state' => 4, 'msj' => 'Usuario esta inactivo. comuníquese con el administrador al siguiente correo: soportefieldservice@tigo.com.co');
            }

            $this->_DB = null;
            echo json_encode($response);

        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }

    }

    public function SolicitaAcceso($data)
    {
        try {

            $pagina = 'Soporte-comercial-(GESCOM)';
            //$pagina = 'seguimiento-pedidos';
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

            //var_dump($response);die();

            return $response;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function recoverPass($data)
    {
        try {
            if ($data['email'] !== $data['emailConfirm']) {
                $response = array('state' => false, 'msj' => 'Los correos no coinciden');
                return $response;
            }

            $correoValido = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if ($correoValido === false) {
                $response = array('state' => false, 'msj' => 'EL formato del correo no es valido');
                return $response;
            }

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT
                                                                            id, cambio_login
                                                                        FROM
                                                                            usuario 
                                                                        WHERE
                                                                            correo = :correo");
            $stmt->execute(array(':correo' => $data['email']));
            if ($stmt->rowCount() === 1) {
                $res = $stmt->fetch(PDO::FETCH_OBJ);

                if ($res->cambio_login === 'SI') {
                    $response = array('state' => false, 'msg' => 'Ya se envío un correo para restablecer la contraseña verifica el siguiente enlace https://web.mail.tigo.com.co/');
                    return $response;
                }

                $datos = ['correo' => $data['email'], 'id' => $res->id];
                $id = json_encode($datos);
                $url = "http://10.100.66.254/BB8/contingencias/Buscar/olvidoPassSoporteComercial/$id";
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
                    $stmt1 = $this->_DB->SeguimientoPedidos()->prepare("UPDATE usuario
                                                    SET cambio_login = 'SI',
                                                    fecha_cambio_login = NOW() 
                                                    WHERE
                                                        id = :id");
                    $stmt1->execute(array(':id' => $res->id));
                    $response = $data;
                } else {
                    $response = $data;
                }
            } else {
                $response = array('state' => false, 'msj' => 'El correo ingresado no se encuentra registrado');
            }
            return $response;
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }

    }
}