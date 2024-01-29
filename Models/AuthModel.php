<?php

require_once '../Class/db.php';

class modelAuth
{

    private $_DB;

    public function __construct()
    {
        $this->_DB = new DB;
    }

    public function Login($data)
    {

        try {
            $Login = $data['username'];
            $pass = $data['password'];

            $pass = md5($pass);

            $stmt = $this->_DB->SeguimientoPedidos()->prepare("SELECT Login, Perfil FROM usuario WHERE Login = :login and Pass = :pass");
            $stmt->execute(array(':login' => $data['username'], ':pass' => md5($data['password'])));
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

                if($stmt->rowCount()){
                    $menu = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $data = array();
                    $data['Perfil'] = $user[0]['Perfil'];
                    $data['Login'] = $user[0]['Login'];
                    $data['menu'] = $menu;

                    $response = array('state' => true, 'data' => $data);
                }else{
                    $response = array('state' => false, 'msj' => 'No se encontraron menu activos para este perfil');
                }



            } else {
                $response = array('state' => false, 'msj' => 'Usuario o Contraseña invalido, inténtelo de nuevo.');
            }

        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
        $this->_DB = null;
        echo json_encode($response);
    }
}